<?php

// phpcs:ignore Generic.Commenting.DocComment.MissingShort
/** @noinspection PhpUndefinedClassInspection */

namespace WPForms\Tasks;

use ActionScheduler;
use ActionScheduler_Action;
use ActionScheduler_DataController;
use ActionScheduler_DBStore;
use WPForms\Helpers\Transient;
use WPForms\Tasks\Actions\EntryEmailsMetaCleanupTask;
use WPForms\Tasks\Actions\EntryEmailsTask;
use WPForms\Tasks\Actions\FormsLocatorScanTask;
use WPForms\Tasks\Actions\AsyncRequestTask;
use WPForms\Tasks\Actions\PurgeSpamTask;

/**
 * Class Tasks manages the tasks queue and provides API to work with it.
 *
 * @since 1.5.9
 */
class Tasks {

	/**
	 * Group that will be assigned to all actions.
	 *
	 * @since 1.5.9
	 */
	const GROUP = 'wpforms';

	/**
	 * Actions setting name.
	 *
	 * @since 1.7.3
	 */
	const ACTIONS = 'actions';

	/**
	 * WPForms pending or in-progress actions.
	 *
	 * @since 1.7.3
	 *
	 * @var array
	 */
	private $active_actions;

	/**
	 * Determine if WPForms task is executing.
	 *
	 * @since 1.9.4
	 *
	 * @var bool
	 */
	private static $task_executing = false;

	/**
	 * Perform certain things on class init.
	 *
	 * @since 1.5.9
	 */
	public function init() {

		// Get WPForms pending or in-progress actions.
		$this->active_actions = $this->get_active_actions();

		// Register WPForms tasks.
		foreach ( $this->get_tasks() as $task ) {

			if ( ! is_subclass_of( $task, Task::class ) ) {
				continue;
			}

			new $task();
		}

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.7.5
	 */
	public function hooks() {

		add_action( 'delete_expired_transients', [ Transient::class, 'delete_all_expired' ], 11 );
		add_action( 'admin_menu', [ $this, 'admin_hide_as_menu' ], PHP_INT_MAX );

		/*
		 * By default we send emails in the same process as the form submission is done.
		 * That means that when many emails are set in form Notifications -
		 * the form submission can take a while because of all those emails that are sending in the background.
		 * Since WPForms 1.6.0 users can enable a new option in Settings > Emails,
		 * called "Optimize Email Sending", to send email in async way.
		 * This feature was enabled for WPForms 1.5.9, but some users were not happy.
		 */
		if ( ! (bool) wpforms_setting( 'email-async', false ) ) {
			add_filter( 'wpforms_tasks_entry_emails_trigger_send_same_process', '__return_true' );
		}

		add_action( EntryEmailsTask::ACTION, [ EntryEmailsTask::class, 'process' ] );
		add_action( 'action_scheduler_after_execute', [ $this, 'clear_action_meta' ], PHP_INT_MAX, 2 );

		add_action( 'action_scheduler_begin_execute', [ $this, 'start_executing' ], 1 );
		add_action( 'action_scheduler_after_execute', [ $this, 'stop_executing' ], 1, 2 );
	}

	/**
	 * Public interface to check if WPForms task is executing.
	 *
	 * @since 1.9.4
	 *
	 * @return bool
	 */
	public static function is_executing(): bool {

		return self::$task_executing;
	}

	/**
	 * Set a flag to indicate that WPForms task is executing.
	 *
	 * @since 1.9.4
	 *
	 * @param int $action_id The action ID to process.
	 */
	public function start_executing( $action_id ) {

		$action_id = (int) $action_id;

		if ( ! class_exists( 'ActionScheduler' ) ) {
			return;
		}

		$store = ActionScheduler::store();

		if ( ! $store ) {
			return;
		}

		$action = $store->fetch_action( $action_id );

		if ( ! $action || $action->get_group() !== self::GROUP ) {
			return;
		}

		self::$task_executing = true;

		/**
		 * Fires before WPForms task is executing.
		 *
		 * @since 1.9.4
		 *
		 * @param int                    $action_id The action ID to process.
		 * @param ActionScheduler_Action $action    Action Scheduler action object.
		 */
		do_action( 'wpforms_tasks_start_executing', $action_id, $action );
	}

	/**
	 * Set a flag to indicate that WPForms task is executing.
	 *
	 * @since 1.9.4
	 *
	 * @param int                    $action_id The action ID to process.
	 * @param ActionScheduler_Action $action    Action Scheduler action object.
	 */
	public function stop_executing( $action_id, $action ) {

		if ( ! $action || ! method_exists( $action, 'get_group' ) || $action->get_group() !== self::GROUP ) {
			return;
		}

		self::$task_executing = false;

		/**
		 * Fires after WPForms task is executed.
		 *
		 * @since 1.9.4
		 *
		 * @param int                    $action_id The action ID to process.
		 * @param ActionScheduler_Action $action    Action Scheduler action object.
		 */
		do_action( 'wpforms_tasks_stop_executing', $action_id, $action );
	}

	/**
	 * Get the list of WPForms default scheduled tasks.
	 * Tasks, that are fired under certain specific circumstances
	 * (like sending form submission email notifications)
	 * are not listed here.
	 *
	 * @since 1.5.9
	 *
	 * @return Task[] List of tasks classes.
	 */
	public function get_tasks() {

		if ( ! $this->is_usable() ) {
			return [];
		}

		$tasks = [
			EntryEmailsMetaCleanupTask::class,
			FormsLocatorScanTask::class,
			AsyncRequestTask::class,
			PurgeSpamTask::class,
		];

		/**
		 * Filters the task class list to initialize.
		 *
		 * @since 1.5.9
		 *
		 * @param array $tasks Task class list.
		 */
		return apply_filters( 'wpforms_tasks_get_tasks', $tasks );
	}

	/**
	 * Hide Action Scheduler admin area when not in debug mode.
	 *
	 * @since 1.5.9
	 * @since 1.9.4 Does not hide the menu when some popular plugins are active.
	 */
	public function admin_hide_as_menu(): void {

		$plugin_exceptions = [
			'action-scheduler/action-scheduler.php',
			'woocommerce/woocommerce.php',
			'wp-rocket/wp-rocket.php',
		];

		/**
		 * Filters the list of plugins for which
		 * the Action Scheduler Tools -> Scheduled Actions menu item
		 * should remain visible.
		 *
		 * @since 1.9.4
		 *
		 * @param array $plugin_exceptions List of plugin exceptions.
		 */
		$plugin_exceptions = apply_filters( 'wpforms_tasks_action_scheduler_tools_plugin_exceptions', $plugin_exceptions );
		$show_as_menu      =
			( defined( 'WPFORMS_SHOW_ACTION_SCHEDULER_MENU' ) && constant( 'WPFORMS_SHOW_ACTION_SCHEDULER_MENU' ) ) ||
			wpforms_debug() ||
			! empty( array_filter( $plugin_exceptions, 'is_plugin_active' ) );
		$hide_as_menu      = ! $show_as_menu;

		/**
		 * Filter to redefine that WPForms hides Tools > Action Scheduler menu item.
		 *
		 * @since 1.5.9
		 *
		 * @param bool $hide_as_menu Hide Tools > Action Scheduler menu item.
		 */
		if ( apply_filters( 'wpforms_tasks_admin_hide_as_menu', $hide_as_menu ) ) {
			remove_submenu_page( 'tools.php', 'action-scheduler' );
		}
	}

	/**
	 * Create a new task.
	 * Used for "inline" tasks, that require additional information
	 * from the plugin runtime before they can be scheduled.
	 *
	 * Example:
	 *     wpforms()->obj( 'tasks' )
	 *              ->create( 'i_am_the_dude' )
	 *              ->async()
	 *              ->params( 'The Big Lebowski', 1998 )
	 *              ->register();
	 *
	 * This `i_am_the_dude` action will be later processed as:
	 *     add_action( 'i_am_the_dude', 'thats_what_you_call_me' );
	 *
	 * Function `thats_what_you_call_me()` will receive `$meta_id` param,
	 * and you will be able to receive all params from the action like this:
	 *     $params = ( new Meta() )->get( (int) $meta_id );
	 *     list( $name, $year ) = $params->data;
	 *
	 * @since 1.5.9
	 *
	 * @param string $action Action that will be used as a hook.
	 *
	 * @return Task
	 */
	public function create( $action ) {

		return new Task( $action );
	}

	/**
	 * Cancel all the AS actions for a group.
	 *
	 * @since 1.5.9
	 *
	 * @param string $group Group to cancel all actions for.
	 */
	public function cancel_all( $group = '' ) {

		if ( empty( $group ) ) {
			$group = self::GROUP;
		} else {
			$group = sanitize_key( $group );
		}

		if ( class_exists( 'ActionScheduler_DBStore' ) ) {
			ActionScheduler_DBStore::instance()->cancel_actions_by_group( $group );
			$this->active_actions = $this->get_active_actions();
		}
	}

	/**
	 * Whether ActionScheduler thinks that it has migrated or not.
	 *
	 * @since 1.5.9.3
	 *
	 * @return bool
	 */
	public function is_usable() {

		// No tasks if ActionScheduler wasn't loaded.
		if ( ! class_exists( 'ActionScheduler_DataController' ) ) {
			return false;
		}

		return ActionScheduler_DataController::is_migration_complete();
	}

	/**
	 * Whether task has been scheduled and is pending or in-progress.
	 *
	 * @since 1.6.0
	 *
	 * @param string $hook Hook to check for.
	 *
	 * @return bool|null
	 * @noinspection PhpUndefinedFunctionInspection
	 */
	public function is_scheduled( $hook ) {

		if ( ! function_exists( 'as_has_scheduled_action' ) ) {
			return null;
		}

		if ( in_array( $hook, $this->active_actions, true ) ) {
			return true;
		}

		// Action is not in the array, so it is not scheduled or belongs to another group.
		return as_has_scheduled_action( $hook );
	}

	/**
	 * Get all WPForms pending or in-progress actions.
	 *
	 * @since 1.7.3
	 */
	private function get_active_actions() {

		global $wpdb;

		$group = self::GROUP;
		$sql   = "SELECT a.hook FROM {$wpdb->prefix}actionscheduler_actions a
					JOIN {$wpdb->prefix}actionscheduler_groups g ON g.group_id = a.group_id
					WHERE g.slug = '$group' AND a.status IN ( 'in-progress', 'pending' )";

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql, 'ARRAY_N' );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		return $results ? array_merge( ...$results ) : [];
	}

	/**
	 * Delete a task by its ID.
	 *
	 * @since 1.9.6.1
	 *
	 * @param int $action_id Action ID.
	 */
	public function delete_action( $action_id ): void {

		global $wpdb;

		$sql = "DELETE FROM {$wpdb->prefix}actionscheduler_actions WHERE action_id = %d";

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $wpdb->prepare( $sql, (int) $action_id ) );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Fetch action by ID.
	 *
	 * @since 1.9.6.1
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return null|ActionScheduler_Action
	 */
	public function fetch_action( $action_id ): ?ActionScheduler_Action {

		if ( ! class_exists( 'ActionScheduler' ) ) {
			return null;
		}

		return ActionScheduler::store()->fetch_action( $action_id );
	}

	/**
	 * Clear the meta after action complete.
	 * Fired before an action is marked as completed.
	 *
	 * @since 1.7.5
	 *
	 * @param integer                $action_id Action ID.
	 * @param ActionScheduler_Action $action    Action name.
	 */
	public function clear_action_meta( $action_id, $action ) {

		$action_schedule = $action->get_schedule();

		if ( $action_schedule === null || $action_schedule->is_recurring() ) {
			return;
		}

		$hook_name = $action->get_hook();

		if ( ! $this->is_scheduled( $hook_name ) ) {
			return;
		}

		$hook_args = $action->get_args();

		if ( ! isset( $hook_args['tasks_meta_id'] ) ) {
			return;
		}

		$meta = new Meta();

		$meta->delete( $hook_args['tasks_meta_id'] );
	}
}

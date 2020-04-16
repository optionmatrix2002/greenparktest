<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\db\Exception;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronController extends Controller
{

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerNotificationsForTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->triggerTicketsMail();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerNotificationsForOverDueTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->ticketOverDues();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerEscalationOneForTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->escalationOneTickets();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerEscalationTwoForTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->escalationTwoTickets();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerEscalationThreeForTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->escalationThreeTickets();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerEscalationFourForTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->escalationFourTickets();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for tickets
     */
    public function actionTriggerEscalationFiveForTickets()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->escalationFiveTickets();

            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for Audits.
     */
    public function actionTriggerNotificationsForAudits()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->triggerAuditsMail();
            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Triggers notifications for Audits.
     */
    public function actionTriggerAuditSubmittedNotifications()
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '1024M');
            \Yii::$app->scheduler->triggerAuditSubmittedNotifications();
            set_time_limit(30);
            ini_set('memory_limit', '128M');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}

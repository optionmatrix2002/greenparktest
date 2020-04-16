<?php
/**
 * Email Component
 * @author Sk.Mehar
 * DateCreated: 11-14-2017
 * Date Modified:
 * Modified By:
 */

namespace app\components;

use app\models\EmailTemplates;
use app\models\Preferences;
use yii;
use yii\base\Component;
use app\models\Tickets;
use app\models\User;

class EmailsComponent extends Component
{

    const FROM = "greenpark.audit@gmail.com";


    const FROM_NAME = "GreenPark";

    const REPLY_TO = "no-reply@gms.com";

    public static $date;

    public function __construct()
    {
        parent::__construct();
        self::$date = date('r');
    }

    public static function sendMail($gridMailArry = [])
    {
        $from = Preferences::getPrefValByName('notification_email');
        $from = $from ? $from : self::FROM;
        $mail = Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($gridMailArry['recipientMail'])
            ->setSubject($gridMailArry['subject'])
            ->setHtmlBody($gridMailArry['message'])
            ->send();


        return $mail;
    }

    public static function sendUserVerificationLinkEmail($username, $recipientMail, $link, $action = '')
    {
        try {
            $message = '';
            $content = '';
            if (($action == "forgot")) {
                $subject = 'Reset your password';
                $content = $subject;
            } else {
                $subject = 'Set your password';
                $content = 'Set your account password';
            }


            $message = "<html><body>";
            $message .= "<table><tr><td>Hi " . $username . ",</td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td>Please click the below link to " . strtolower($content) . ".</td></tr>";
            $message .= "<tr><td>" . $link . "</td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td>Best Regards,</td></tr>";
            $message .= "<tr><td>Green Park Corporate Audit Team.</td></tr></table>";
            $message .= "</body></html>";

            $gridMailArry = [
                'recipientMail' => $recipientMail,
                'subject' => $subject,
                'message' => $message
            ];
            return self::sendMail($gridMailArry);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $toMail
     * @param $zipFolder
     */
    public static function sendAuditReportToUser($toMail, $zipFolder, $subject,$auditName)
    {
        try {
            $downLoadFolder = $auditName;
            $message = "<html><body>";
            $message .= "<table><tr><td>Hi,</td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td>Please <a href = ".$downLoadFolder.">click here</a>  to download the audit report.</td></tr>";
    
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td></td></tr>";
            $message .= "<tr><td>Best Regards,</td></tr>";
            $message .= "<tr><td>Green Park Corporate Audit Team.</td></tr></table>";
            $message .= "</body></html>";

            $gridMailArry = [
                'recipientMail' => $toMail,
                'subject' => $subject,
                'message' => $message
            ];
            
            $from = Preferences::getPrefValByName('notification_email');
            $from = $from ? $from : self::FROM;
            
            $mail = Yii::$app->mailer->compose()
                ->setFrom($from)
                ->setTo($gridMailArry['recipientMail'])
                ->setSubject($gridMailArry['subject'])
                //->attach($zipFolder)
                ->setHtmlBody($gridMailArry['message'])
                ->send();

            return $mail;

        } catch (\Exception $e) {
            //print_r($e->getMessage());
            throw $e;
        }
    }
    
    public static function sendNonComplaintToDepartment($toMails, $ticket_name, $user_id)
    {
        try {
            
            $model =EmailTemplates::findOne(['template_id'=>1]);
            $tickets_model =Tickets::findOne(['ticket_name'=>$ticket_name]);
            $name='';
            if($user_id){
                $user_details=User::findOne(['user_id'=>$user_id]);
                $name = $user_details->first_name.' '.$user_details->last_name;
            }
            $message1=str_replace('&lt;&lt;date&gt;&gt;', date('d/F/Y'), $model->email_content);
            
            $message2=str_replace('&lt;&lt;department&gt;&gt;', $tickets_model->department->department_name, $message1);
           
            $html = '<table><tbody><tr style="height: 80px;padding-left: 30px;">
                    <td style="width: 100%;height: 80px;padding-left: 30px;">
                    <p><span style="color: black;">Ticket No: '.$ticket_name.'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Subject: '.$tickets_model->subject.'</span><span style="color: black;">&nbsp;</span></p>
                    <p><span style="color: black;">Observation : '.$tickets_model->description.'</span></p>
                    </td>
                    </tr></tbody></table>';   
            $message3=str_replace('&lt;&lt;ticket&gt;&gt;', $html, $message2);
            
            /*$message3=str_replace('&lt;&lt;ticketnumber&gt;&gt;', $ticket_name, $message2);
            
            $message4=str_replace('&lt;&lt;subject&gt;&gt;', $tickets_model->subject, $message3);
            
            $message5=str_replace('&lt;&lt;observation&gt;&gt;', $tickets_model->description, $message4);*/
            
            $message6=str_replace('&lt;&lt;hotel&gt;&gt;', $tickets_model->hotel->hotel_name, $message3);
            
            $message=str_replace('&lt;&lt;departmenthead&gt;&gt;', $name, $message6);
            
            $subject="MEMO ISSUE FOR : ".$tickets_model->hotel->hotel_name;
           
                    
            $gridMailArry = [
                'recipientMail' => $toMails,
                'subject' => $subject,
                'message' => $message
            ];
            
            $from = Preferences::getPrefValByName('notification_email');
            $from = $from ? $from : self::FROM;
            
            $mail = Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($gridMailArry['recipientMail'])
            ->setSubject($gridMailArry['subject'])
            //->attach($zipFolder)
            ->setHtmlBody($gridMailArry['message'])
            ->send();
            
            return $mail;
            
        } catch (\Exception $e) {
            //print_r($e->getMessage());
            throw $e;
        }
    }
    
    public static function sendConsolidateNonComplaintToDepartment($toMails, $tickets, $user_id)
    {
        try {
            
            $model =EmailTemplates::findOne(['template_id'=>1]);
            $tickets_model =Tickets::findOne(['ticket_name'=>$tickets[0]["ticketnumber"]]);
            $name='';
            if($user_id){
                $user_details=User::findOne(['user_id'=>$user_id]);
                $name = $user_details->first_name.' '.$user_details->last_name;
            }
            $message1=str_replace('&lt;&lt;date&gt;&gt;', date('d/F/Y'), $model->email_content);
            
            $message2=str_replace('&lt;&lt;department&gt;&gt;', $tickets_model->department->department_name, $message1);
            
          //  $message3=str_replace('&lt;&lt;ticketnumber&gt;&gt;', $ticket_name, $message2);
            
        //    $message4=str_replace('&lt;&lt;subject&gt;&gt;', $tickets_model->subject, $message3);
            
       //     $message5=str_replace('&lt;&lt;observation&gt;&gt;', $tickets_model->description, $message4);
            
            
            
            $subject="MEMO ISSUE FOR : ".$tickets_model->hotel->hotel_name;
            
            $html='<table><tbody>';
            
            foreach ($tickets as $ticket){
                $html .= '<tr style="height: 80px;padding-left: 30px;">
                            <td style="width: 100%;height: 80px;padding-left: 30px;">
                            <p><span style="color: black;">Ticket No: '.$ticket["ticketnumber"].'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Subject: '.$ticket["subject"].'</span><span style="color: black;">&nbsp;</span></p>
                            <p><span style="color: black;">Observation : '.$ticket["observation"].'</span></p>
                            </td>
                            </tr>';              
            }
            $html .= '</tbody></table>';
            
            $message3=str_replace('&lt;&lt;ticket&gt;&gt;', $html, $message2);
            
            $message4=str_replace('&lt;&lt;hotel&gt;&gt;', $tickets_model->hotel->hotel_name, $message3);
            
            $message=str_replace('&lt;&lt;departmenthead&gt;&gt;', $name, $message4);
           
            $gridMailArry = [
                'recipientMail' => $toMails,
                'subject' => $subject,
                'message' => $message
            ];
            
            $from = Preferences::getPrefValByName('notification_email');
            $from = $from ? $from : self::FROM;
            
            $mail = Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTo($gridMailArry['recipientMail'])
            ->setSubject($gridMailArry['subject'])
            //->attach($zipFolder)
            ->setHtmlBody($gridMailArry['message'])
            ->send();
            
            return $mail;
            
        } catch (\Exception $e) {
            //print_r($e->getMessage());
            throw $e;
        }
    }
}


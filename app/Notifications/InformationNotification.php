<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Information;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InformationNotification extends Notification
{
    use Queueable;
    private Information $information;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Information $information)
    {
        //
         $this->information = $information;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    // /**
    //  * Get the mail representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return \Illuminate\Notifications\Messages\MailMessage
    //  */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
            'date' => $this->information->date,
            'content' => $this->information->content,
            'user_id'=>$this->information->user_id,
            'seller_id'=>$this->information->seller_id,
        ];
    }
}

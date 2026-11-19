<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuratMasukDisposisiNotification extends Notification
{
    use Queueable;

    public $surat;

    /**
     * Create a new notification instance.
     */
    public function __construct($surat)
    {
        $this->surat = (object) $surat;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $noAgendaText = $this->surat->no_agenda ? " (No. Agenda: #{$this->surat->no_agenda})" : '';
        $url = route('surat.index', ['search' => $this->surat->nomor_surat]);

        return (new MailMessage)
            ->subject('Pemberitahuan Surat Masuk: ' . $this->surat->nomor_surat)
            ->greeting('Yth. ' . $notifiable->name . ',')
            ->line('Anda telah ditunjuk sebagai tujuan / penerima disposisi surat masuk baru:')
            ->line('• Nomor Surat: ' . $this->surat->nomor_surat . $noAgendaText)
            ->line('• Asal / Pengirim: ' . ($this->surat->asal_surat ?? '-'))
            ->line('• Perihal: ' . $this->surat->perihal)
            ->line('• Tanggal Surat: ' . date('d/m/Y', strtotime($this->surat->tanggal_surat)))
            ->action('Buka & Lihat Surat', $url)
            ->line('Silakan login ke aplikasi E-SURAT untuk menindaklanjuti disposisi surat tersebut.')
            ->salutation('Salam hormat,' . "\n" . config('app.name', 'E-SURAT SMK Negeri 1 Sadaniang'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'surat_id' => $this->surat->id,
            'nomor_surat' => $this->surat->nomor_surat,
            'no_agenda' => $this->surat->no_agenda ?? null,
            'perihal' => $this->surat->perihal,
            'asal_surat' => $this->surat->asal_surat ?? '-',
            'title' => 'Surat Masuk Baru Ditujukan Kepada Anda',
            'message' => 'Surat no. ' . $this->surat->nomor_surat . ' dari ' . ($this->surat->asal_surat ?? '-') . ' telah ditujukan kepada Anda.',
            'url' => route('surat.index', ['search' => $this->surat->nomor_surat]),
            'created_at' => now()->toIso8601String(),
        ];
    }
}

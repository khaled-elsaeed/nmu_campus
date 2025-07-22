<x-mail::message>
{{-- Greeting --}}
# 🎉 Reservation Activated!

Dear {{ $notifiable->name ?? 'User' }},

We are pleased to inform you that your reservation has been **successfully activated**. Please find your reservation details below:

<x-mail::panel>
**Academic Term:** <br>
**Start Date:** <br>
**End Date:** <br>
**Status:** Active
</x-mail::panel>

Your reservation is now active and ready for use. You can access your account to view more details about your reservation.

<x-mail::button :url="config('app.url') . '/dashboard'">
View Dashboard
</x-mail::button>

If you have any questions or need further assistance, please contact your housing manager or the IT support team at NMU.

Thank you for choosing {{ config('app.name') }}.

Best regards,<br>
{{ config('app.name') }} Team

{{-- Footer --}}
<x-slot:subcopy>
This is an automated message. Please do not reply directly to this email.
</x-slot:subcopy>
</x-mail::message>
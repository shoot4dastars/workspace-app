<x-mail::message>
# Workspace Invitation

{{ $invite->inviter->name }} has invited you to join the workspace **{{ $invite->workspace->name }}**.

<x-mail::button :url="route('invites.accept', $invite->token)">
Accept Invite
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

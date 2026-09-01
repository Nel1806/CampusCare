<h1>Maintenance Request Resolved</h1>

<p>Hello {{ $maintenanceRequest->requester_name }},</p>

<p>The maintenance issue you reported has been successfully resolved.</p>

<p><strong>Request ID:</strong> {{ $maintenanceRequest->reference }}<br>
<strong>Reported Issue:</strong> {{ $maintenanceRequest->description }}<br>
<strong>Building:</strong> {{ $maintenanceRequest->building }}<br>
<strong>Room / Area:</strong> {{ $maintenanceRequest->room }}<br>
<strong>Final Status:</strong> Resolved<br>
<strong>Date Resolved:</strong> {{ $maintenanceRequest->updated_at->timezone('Asia/Manila')->format('F j, Y g:i A') }}</p>

<p>Thank you,<br>CampusCare Facility Support</p>

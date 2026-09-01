<h1>Maintenance Request Received</h1>

<p>Hello {{ $maintenanceRequest->requester_name }},</p>

<p>Your maintenance request has been successfully received. Please keep your Request ID for reference.</p>

<p><strong>Request ID:</strong> {{ $maintenanceRequest->reference }}<br>
<strong>Building:</strong> {{ $maintenanceRequest->building }}<br>
<strong>Room / Area:</strong> {{ $maintenanceRequest->room }}<br>
<strong>Reported Problem:</strong> {{ $maintenanceRequest->description }}<br>
<strong>Current status:</strong> {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}<br>
<strong>Date Submitted:</strong> {{ $maintenanceRequest->created_at->timezone('Asia/Manila')->format('F j, Y g:i A') }}</p>

<p>Thank you,<br>CampusCare Facility Support</p>

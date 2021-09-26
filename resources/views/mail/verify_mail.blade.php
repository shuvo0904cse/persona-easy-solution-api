<!DOCTYPE html>
<html lang="en">

<body>
	
<p>Dear {{ $user->name }}</p>
<p>Your account has been created, please activate your account by clicking this link</p>
<p><a href="{{ url('api/verify/'.$user->email_verification_token) }}">
		{{ url('api/verify/'.$user->email_verification_token) }}
</a></p>

<p>Thanks</p>

</body>

</html> 
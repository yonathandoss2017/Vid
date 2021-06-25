<html>
	<form action="https://dev.vidoomy.com/login_check" method="POST" id="tolog">
		<input type="hidden" value="" name="_username">
		<input type="hidden" value="" name="_password">
		<input type="hidden" value="<?php echo bin2hex(random_bytes(32)); ?>" name="_csrf_token">
	</form>
	<script>
		document.getElementById('tolog').submit();
	</script>
</html>

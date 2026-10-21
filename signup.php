<?php
require_once __DIR__ . '/classes/dbh.classes.php';
require_once __DIR__ . '/classes/settings.php';

$signupErrorMessages = [
    'emptyinput' => 'Complete every required field.',
    'invalidusername' => 'Use letters and numbers only for your username.',
    'invalidemail' => 'Enter a valid email address.',
    'passwordmismatch' => 'The passwords do not match.',
    'usernameoremailtaken' => 'That username or email is already in use.',
    'registrationclosed' => 'New account registration is currently paused.',
];
$errorCode = $_GET['error'] ?? '';
$signupError = is_string($errorCode) ? ($signupErrorMessages[$errorCode] ?? '') : '';
$registration = (new Settings())->getRegistrationStatus();
$registrationClosed = $registration['available'] && $registration['status'] !== 'active';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Create Account — EARN-NOW</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
	*{ box-sizing: border-box; }

	:root{
		--gold: #d4af7a;
		--gold-bright: #f0cd93;
		--ink: #f4f1ea;
		--dim: #9aa0b4;
		--faint: #6b7086;
		--surface: rgba(255,255,255,0.045);
		--border: rgba(255,255,255,0.1);
	}

	html, body{
		margin: 0;
		padding: 0;
		min-height: 100vh;
		background: var(--ink);
		font-family: 'Inter', sans-serif;
	}

	.screen{
		min-height: 100vh;
		display: flex;
	}

	/* -------- Left brand panel -------- */
	.brand-panel{
		position: relative;
		flex: 1.1;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		padding: 64px;
		overflow: hidden;
		background:
			radial-gradient(900px 650px at 15% 10%, rgba(212,175,122,0.14) 0%, transparent 55%),
			radial-gradient(700px 600px at 90% 90%, rgba(122,162,242,0.10) 0%, transparent 55%),
			linear-gradient(160deg, #0a0c14 0%, #10131f 55%, #161a2b 100%);
	}

	.brand-panel::before{
		content: "";
		position: absolute;
		inset: 0;
		background-image:
			linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
			linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
		background-size: 42px 42px;
		mask-image: radial-gradient(circle at 30% 20%, black, transparent 70%);
		pointer-events: none;
	}

	.brand-mark{
		position: relative;
		display: flex;
		align-items: center;
		gap: 10px;
		font-family: 'Fraunces', serif;
		font-weight: 500;
		font-size: 20px;
		letter-spacing: 1.5px;
		color: var(--ink);
		text-transform: uppercase;
	}
	.brand-mark .dot{
		width: 9px;
		height: 9px;
		border-radius: 50%;
		background: var(--gold);
		box-shadow: 0 0 16px 3px rgba(212,175,122,0.7);
	}

	.brand-copy{
		position: relative;
		max-width: 420px;
	}
	.brand-copy .eyebrow{
		font-size: 12px;
		letter-spacing: 3px;
		text-transform: uppercase;
		color: var(--gold);
		font-weight: 600;
		margin-bottom: 22px;
	}
	.brand-copy h1{
		font-family: 'Fraunces', serif;
		font-weight: 500;
		font-size: 46px;
		line-height: 1.15;
		color: var(--ink);
		margin: 0 0 20px;
	}
	.brand-copy h1 em{
		font-style: italic;
		color: var(--gold-bright);
	}
	.brand-copy p{
		font-size: 15px;
		line-height: 1.7;
		color: var(--dim);
		margin: 0;
	}

	.brand-list{
		position: relative;
		display: flex;
		flex-direction: column;
		gap: 16px;
		padding-top: 40px;
		border-top: 1px solid rgba(255,255,255,0.08);
	}
	.brand-list div{
		display: flex;
		align-items: center;
		gap: 12px;
		font-size: 13.5px;
		color: var(--dim);
	}
	.brand-list div .check{
		width: 20px;
		height: 20px;
		border-radius: 50%;
		background: rgba(212,175,122,0.14);
		border: 1px solid rgba(212,175,122,0.4);
		color: var(--gold-bright);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 11px;
		flex-shrink: 0;
	}

	/* -------- Right form panel -------- */
	.form-panel{
		flex: 1;
		min-width: 420px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: #0d0f18;
		padding: 40px 32px;
	}

	.auth-card{
		width: 100%;
		max-width: 400px;
	}

	.auth-card .kicker{
		font-size: 12px;
		letter-spacing: 2px;
		text-transform: uppercase;
		color: var(--faint);
		margin-bottom: 10px;
	}

	.auth-card h2{
		font-family: 'Fraunces', serif;
		font-weight: 500;
		font-size: 32px;
		color: var(--ink);
		margin: 0 0 8px;
	}

	.auth-card .sub{
		color: var(--dim);
		font-size: 14px;
		margin: 0 0 32px;
	}

	.field{
		margin-bottom: 18px;
	}
	.field.split{
		display: flex;
		gap: 14px;
	}
	.field.split > div{ flex: 1; }
	.field label{
		display: block;
		font-size: 11px;
		font-weight: 600;
		letter-spacing: 0.6px;
		text-transform: uppercase;
		color: var(--dim);
		margin-bottom: 8px;
	}
	.field input{
		width: 100%;
		padding: 14px 16px;
		background: var(--surface);
		border: 1px solid var(--border);
		border-radius: 10px;
		color: var(--ink);
		font-family: 'Inter', sans-serif;
		font-size: 14.5px;
		transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
	}
	.field input::placeholder{ color: var(--faint); }
	.field input:focus{
		outline: none;
		border-color: var(--gold);
		background: rgba(255,255,255,0.06);
		box-shadow: 0 0 0 4px rgba(212,175,122,0.14);
	}
	.form-notice{
		margin: 0 0 18px;
		padding: 12px 14px;
		border: 1px solid rgba(242,122,122,0.3);
		border-radius: 9px;
		color: #ffc1c1;
		background: rgba(242,122,122,0.08);
		font-size: 13px;
		line-height: 1.55;
	}

	.terms{
		display: flex;
		align-items: flex-start;
		gap: 10px;
		margin: 4px 0 26px;
		font-size: 12.5px;
		color: var(--dim);
		line-height: 1.5;
	}
	.terms input{ margin-top: 3px; accent-color: var(--gold); }
	.terms a{ color: var(--gold-bright); text-decoration: none; }
	.terms a:hover{ text-decoration: underline; }

	.btn-gold{
		width: 100%;
		padding: 15px;
		border: none;
		border-radius: 10px;
		background: linear-gradient(135deg, var(--gold) 0%, #b8905a 100%);
		color: #14151c;
		font-family: 'Inter', sans-serif;
		font-weight: 700;
		font-size: 14.5px;
		letter-spacing: 0.4px;
		cursor: pointer;
		box-shadow: 0 16px 32px -12px rgba(212,175,122,0.55);
		transition: transform .18s ease, box-shadow .18s ease;
	}
	.btn-gold:hover{
		transform: translateY(-2px);
		box-shadow: 0 20px 38px -12px rgba(212,175,122,0.7);
	}
	.btn-gold:active{ transform: translateY(0); }

	.divider{
		display: flex;
		align-items: center;
		gap: 14px;
		margin: 28px 0;
		color: var(--faint);
		font-size: 12px;
		text-transform: uppercase;
		letter-spacing: 1px;
	}
	.divider::before, .divider::after{
		content: "";
		flex: 1;
		height: 1px;
		background: rgba(255,255,255,0.08);
	}

	.footer-line{
		text-align: center;
		font-size: 13.5px;
		color: var(--dim);
	}
	.footer-line a{
		color: var(--gold-bright);
		text-decoration: none;
		font-weight: 600;
	}
	.footer-line a:hover{ text-decoration: underline; }

	@media (max-width: 900px){
		.brand-panel{ display: none; }
		.form-panel{ min-width: 0; }
	}
</style>
</head>
<body>

<div class="screen">

	<div class="brand-panel">
		<div class="brand-mark"><span class="dot"></span>EARN&#8209;NOW</div>

		<div class="brand-copy">
			<div class="eyebrow">Join the Vault</div>
			<h1>Your earnings deserve <em>a home</em> this refined.</h1>
			<p>Create your account in under a minute and get a precise, private view of everything you earn — from your first dollar to your millionth.</p>
		</div>

		<div class="brand-list">
			<div><span class="check">&#10003;</span> Bank-level encrypted profile</div>
			<div><span class="check">&#10003;</span> Real-time earnings tracking</div>
			<div><span class="check">&#10003;</span> No hidden fees, ever</div>
		</div>
	</div>

	<div class="form-panel">
		<div class="auth-card">
			<div class="kicker">Get started</div>
			<h2>Create your account</h2>
			<p class="sub">A few details and you're in.</p>

			<?php if ($signupError !== ''): ?>
				<div class="form-notice" role="alert"><?php echo htmlspecialchars($signupError, ENT_QUOTES, 'UTF-8'); ?></div>
			<?php endif; ?>
			<?php if ($registrationClosed): ?>
				<div class="form-notice" role="status">Registration is paused by an administrator. You can still sign in to an existing account.</div>
			<?php endif; ?>

			<form method="post" action="includes/signup.inc.php">
				<div class="field">
					<label for="user">Username</label>
					<input type="text" id="user" placeholder="Choose a username" name="uid" autocomplete="username" <?php echo $registrationClosed ? 'disabled' : ''; ?>>
				</div>

				<div class="field">
					<label for="email">Email</label>
					<input type="text" id="email" placeholder="you@example.com" name="email" autocomplete="email" <?php echo $registrationClosed ? 'disabled' : ''; ?>>
				</div>

				<div class="field split">
					<div>
						<label for="password">Password</label>
						<input type="password" id="password" placeholder="Create a password" name="pwd" autocomplete="new-password" <?php echo $registrationClosed ? 'disabled' : ''; ?>>
					</div>
					<div>
						<label for="password2">Re-enter</label>
						<input type="password" id="password2" placeholder="Confirm password" name="pwdrepeat" autocomplete="new-password" <?php echo $registrationClosed ? 'disabled' : ''; ?>>
					</div>
				</div>

				<label class="terms">
					<input type="checkbox" <?php echo $registrationClosed ? 'disabled' : ''; ?>>
					<span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
				</label>

				<input type="submit" class="btn-gold" name="submit" value="Create Account" <?php echo $registrationClosed ? 'disabled' : ''; ?>>
			</form>

			<div class="divider">or</div>

			<p class="footer-line">Already have an account? <a href="login.php">Sign in</a></p>
		</div>
	</div>

</div>
</body>
</html>

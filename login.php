<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Sign In — EARN-NOW</title>
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

	.brand-stats{
		position: relative;
		display: flex;
		gap: 48px;
		padding-top: 40px;
		border-top: 1px solid rgba(255,255,255,0.08);
	}
	.brand-stats div strong{
		display: block;
		font-family: 'Fraunces', serif;
		font-size: 26px;
		font-weight: 500;
		color: var(--ink);
	}
	.brand-stats div span{
		font-size: 11px;
		letter-spacing: 1px;
		text-transform: uppercase;
		color: var(--faint);
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
		max-width: 380px;
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
		margin: 0 0 36px;
	}

	.field{
		margin-bottom: 20px;
	}
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

	.row-between{
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin: -6px 0 28px;
		font-size: 13px;
	}
	.remember{
		display: flex;
		align-items: center;
		gap: 8px;
		color: var(--dim);
	}
	.remember input{ accent-color: var(--gold); }
	.row-between a{
		color: var(--gold-bright);
		text-decoration: none;
	}
	.row-between a:hover{ text-decoration: underline; }

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
		margin: 30px 0;
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
			<div class="eyebrow">Private Access</div>
			<h1>Where every <em>gram</em> of your earnings<br>is accounted for.</h1>
			<p>A quiet, precise home for your income — built for people who treat every dollar like it matters. Sign in to pick up right where you left off.</p>
		</div>

		<div class="brand-stats">
			<div><strong>128K+</strong><span>Members</span></div>
			<div><strong>$42M</strong><span>Tracked Monthly</span></div>
			<div><strong>99.98%</strong><span>Uptime</span></div>
		</div>
	</div>

	<div class="form-panel">
		<div class="auth-card">
			<div class="kicker">Welcome back</div>
			<h2>Sign in</h2>
			<p class="sub">Enter your credentials to access your dashboard.</p>

			<form method="post" action="includes/login.inc.php">
				<div class="field">
					<label for="user">Username</label>
					<input type="text" id="user" placeholder="Enter your username" name="uid" autocomplete="username">
				</div>
				<div class="field">
					<label for="password">Password</label>
					<input type="password" id="password" placeholder="Enter your password" name="pwd" autocomplete="current-password">
				</div>

				<input type="submit" class="btn-gold" value="Sign In">
			</form>

			<div class="divider">or</div>

			<p class="footer-line">Don't have an account? <a href="signup.php">Create one</a></p>
		</div>
	</div>

</div>
</body>
</html>

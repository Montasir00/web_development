console.log("otp.js loaded");
document.addEventListener('DOMContentLoaded', function () {
  // Initialize only once
  if (window.otpInitialized) return;
  window.otpInitialized = true;

  const sendOtpBtn = document.getElementById('send-otp-btn');
  const backToEmailBtn = document.getElementById('back-to-email');
  const emailStep = document.getElementById('email-step');
  const otpStep = document.getElementById('otp-step');
  const emailInput = document.getElementById('telegram-email-display'); // Fixed ID

  if (sendOtpBtn && emailInput) {
      sendOtpBtn.addEventListener('click', function () {
        console.log("Send OTP clicked");
          const email = emailInput.value.trim();

          if (!email) {
              alert('Please enter your email address');
              return;
          }

          this.value = 'Sending...';
          this.disabled = true;

          fetch('/otp-service/send_otp.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email: email })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  if (emailStep) emailStep.style.display = 'none';
                  if (otpStep) otpStep.style.display = 'block';
              } else {
                  alert(data.message || 'Failed to send OTP.');
              }
          })
          .catch(err => {
              console.error(err);
              alert('Error sending OTP. Please try again.');
          })
          .finally(() => {
              this.value = 'Send OTP';
              this.disabled = false;
          });
      });
  }

  if (backToEmailBtn && emailStep && otpStep) {
      backToEmailBtn.addEventListener('click', function (e) {
          e.preventDefault();
          emailStep.style.display = 'block';
          otpStep.style.display = 'none';
          const otpInput = document.querySelector('#otp-step input[name="otp"]');
          if (otpInput) otpInput.value = '';
      });
  }
});
function showOtpStep() {
    document.getElementById('otp-step').style.display = 'block';
    document.querySelector('#otp-step input[name="otp"]').required = true;
}

// Example: Hide OTP step and remove required
function hideOtpStep() {
    document.getElementById('otp-step').style.display = 'none';
    document.querySelector('#otp-step input[name="otp"]').required = false;
}
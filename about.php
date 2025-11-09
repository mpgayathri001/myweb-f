<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About | BMMS Motors</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: #fff;
      overflow-x: hidden;
    }

    .section {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 60px 80px;
    }

    .section img {
      width: 45%;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255,255,255,0.1);
    }

    .content {
      width: 50%;
      padding: 40px;
    }

    .content h1 {
      font-size: 40px;
      color: #ffcc00;
      margin-bottom: 15px;
      text-shadow: 2px 2px 6px rgba(255, 255, 255, 0.2);
    }

    .content h2 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #ffa726;
    }

    .content p {
      line-height: 1.8;
      font-size: 16px;
      color: #ddd;
      margin-bottom: 20px;
      text-align: justify;
    }

    .partners {
      margin-top: 20px;
      font-style: italic;
      color: #ccc;
    }

    .logo {
      width: 30px;
      height: 90px;
      margin-bottom: 20px;
      border-radius: 50%;
      border: 0px solid #ffcc00;
    }

    .reverse {
      flex-direction: row-reverse;
      background: #111;
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #111;
      color: #aaa;
      border-top: 1px solid #222;
    }

    @media (max-width: 900px) {
      .section {
        flex-direction: column;
        padding: 40px 20px;
      }

      .section img, .content {
        width: 100%;
      }

      .content {
        text-align: center;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- First Section -->
  <div class="section">
    <img src="aboutimage.png" alt="BMMS Motors Image">
    <div class="content">
      <img src="bmmslogo.png" class="logo" alt="BMMS Logo">
      <h1>BMMS MOTORS ⚡</h1>
      <h2>Driven by Innovation, Powered by Vision</h2>
      <p>
        Founded with a dream to redefine sustainable mobility, <strong>BMMS Motors</strong> began its journey in 
        <strong>2024</strong> with a vision to bring affordable and reliable electric 3-wheelers to every doorstep.
        We believe that every drive should be efficient, eco-friendly, and empowering — that’s what fuels our mission every day.
      </p>
      <p class="partners">Partnership of <strong>Balan</strong>, <strong>Sathish</strong>, and <strong>Mani</strong> — the trio that turned vision into motion.</p>
    </div>
  </div>

  <!-- Second Section (Reverse Layout) -->
  <div class="section reverse">
    <img src="aboutvorc.png" alt="VORC Motors">
    <div class="content">
      <h1>VORC MOTORS</h1>
      <h2>Our Proud Dealership Partner</h2>
      <p>
        We are the authorized dealer of <strong>VORC Motors</strong> — a brand known for its commitment to quality, 
        performance, and innovation in the electric vehicle industry. Together, we aim to accelerate the shift 
        toward a cleaner and more connected transportation future.
      </p>
      <p>
        From passenger models to load carriers, our collaboration ensures every vehicle we deliver stands for 
        durability, efficiency, and customer satisfaction.
      </p>
    </div>
  </div>

  <footer>
    © <?php echo date("Y"); ?> BMMS Motors. All Rights Reserved.
  </footer>

</body>
</html>

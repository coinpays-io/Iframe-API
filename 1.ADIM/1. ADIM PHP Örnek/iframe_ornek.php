<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Örnek Ödeme Sayfası</title>
</head>
<body>

<div>
    <h1>Örnek Ödeme Sayfası</h1>
	<p>1. ADIM için örnek kodlar</p>
</div>
<br><br>

<div style="width: 100%;margin: 0 auto;display: table;">

	<?php

	## 1. ADIM için örnek kodlar ##

	####################### DÜZENLEMESİ ZORUNLU ALANLAR #######################
	#
	## API Entegrasyon Bilgileri - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
	$merchant_id 	= 'XXXXXX';
	$merchant_key 	= 'YYYYYYYYYYYYYY';
	$merchant_salt	= 'ZZZZZZZZZZZZZZ';
	#
	## Müşterinizin sitenizde kayıtlı veya form vasıtasıyla aldığınız eposta adresi
	$email = "XXXXXXXX";
	#
	## Tahsil edilecek tutar.
	$payment_amount	= ""; //9.99 için 9.99 * 100 = 999 gönderilmelidir.
	#
	## Sipariş numarası: Her işlemde benzersiz olmalıdır!! Bu bilgi bildirim sayfanıza yapılacak bildirimde geri gönderilir.
	$merchant_oid = "";
	#
	## Müşterinizin sitenizde kayıtlı veya form aracılığıyla aldığınız ad ve soyad bilgisi
	$user_name = "";
	#
	## Müşterinizin sitenizde kayıtlı veya form aracılığıyla aldığınız adres bilgisi
	$user_address = "";
	#
	## Müşterinizin sitenizde kayıtlı veya form aracılığıyla aldığınız telefon bilgisi
	$user_phone = "";
	#
	## Ödeme bekleniyor sayfası sonrası müşterinizin yönlendirileceği sayfa
	## !!! Bu sayfa siparişi onaylayacağınız sayfa değildir! Yalnızca müşterinizi bilgilendireceğiniz sayfadır!
	## !!! Siparişi onaylayacağız sayfa "Bildirim URL" sayfasıdır (Bakınız: 2.ADIM Klasörü).
	$merchant_pending_url = "http://www.siteniz.com/odeme_onaylaniyor.php";
	#
	## Müşterinin sepet/sipariş içeriği
	$user_basket = "";
	#
	/* ÖRNEK $user_basket oluşturma - Ürün adedine göre array'leri çoğaltabilirsiniz
	$user_basket = base64_encode(json_encode(array(
		array("Örnek ürün 1", "18.00", 1), // 1. ürün (Ürün Ad - Birim Fiyat - Adet )
		array("Örnek ürün 2", "33.25", 2), // 2. ürün (Ürün Ad - Birim Fiyat - Adet )
		array("Örnek ürün 3", "45.42", 1)  // 3. ürün (Ürün Ad - Birim Fiyat - Adet )
	)));
	*/
	############################################################################################

	## Kullanıcının IP adresi
	if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	} elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip = $_SERVER["REMOTE_ADDR"];
	}

	## !!! Eğer bu örnek kodu sunucuda değil local makinanızda çalıştırıyorsanız
	## buraya dış ip adresinizi (https://www.whatismyip.com/) yazmalısınız. Aksi halde geçersiz coinpays_token hatası alırsınız.
	$user_ip=$ip;
	##

    ## Mağaza canlı modda iken test işlem yapmak için 1 olarak gönderilebilir.
    $test_mode = 0;

	## Burada ödeme sayfanızın hangi dilde görüntülemek istediğinizi seçebilirsiniz. Aşağıda örnek değerler mevcut. Son güncel değerlere erişmek için
	## (https://coinpays.io/shared/languages) adresini ziyaret edin
    $lang=  "tr"; //tr-en-de-fr-es-kr-jp-ar-ru-cn-id-ua

	## Burada sepetinizin hangi para biriminde görüntülemek istediğinizi seçebilirsiniz. Aşağıda örnek değerler mevcut. Son güncel değerlere erişmek için
	## (https://coinpays.io/shared/currencies) adresini ziyaret edin
	$currency = "TL";//USD-EUR-TRY-GBP-RUB-CNY-KRW
	
	####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
	$hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket;
	$coinpays_token=base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
	$post_vals=array(
		'merchant_id'=>$merchant_id,
		'user_ip'=>$user_ip,
		'lang'=>$lang,
		'currency' => $currency,
		'merchant_oid'=>$merchant_oid,
		'email'=>$email,
		'payment_amount'=>$payment_amount,
		'coinpays_token'=>$coinpays_token,
		'user_basket'=>$user_basket,
		'user_name'=>$user_name,
		'user_address'=>$user_address,
		'user_phone'=>$user_phone,
		'merchant_pending_url'=>$merchant_pending_url,
		'test_mode' => $test_mode
		);
	
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://app.coinpays.io/api/get-token");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1) ;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	
	 // XXX: DİKKAT: lokal makinanızda "SSL certificate problem: unable to get local issuer certificate" uyarısı alırsanız eğer
     // aşağıdaki kodu açıp deneyebilirsiniz. ANCAK, güvenlik nedeniyle sunucunuzda (gerçek ortamınızda) bu kodun kapalı kalması çok önemlidir!
     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	 
	$result = @curl_exec($ch);

	if(curl_errno($ch))
		die("COINPAYS IFRAME connection error. err:".curl_error($ch));

	curl_close($ch);
	
	$result=json_decode($result,1);
		
	if($result['status']=='success')
		$token=$result['token'];
	else
		die("COINPAYS IFRAME failed. reason:".$result['reason']);
	#########################################################################

	?>

	<!-- Ödeme formunun açılması için gereken HTML kodlar / Başlangıç -->
    <script src="https://app.coinpays.io/assets/js/iframeResizer.min.js"></script>
    <iframe src="https://app.coinpays.io/payment/<?php echo $token;?>" id="coinpaysiframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
	<script>iFrameResize({},'#coinpaysiframe');</script>
	<!-- Ödeme formunun açılması için gereken HTML kodlar / Bitiş -->

</div>

<br><br>
</body>
</html>

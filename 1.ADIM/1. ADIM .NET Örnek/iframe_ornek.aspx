<%@ Page Language="C#" AutoEventWireup="true" CodeFile="iframe_ornek.aspx.cs" Inherits="WebApplication3._Default" %>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>COINPAYS Ödeme .NET Örnek Sayfa</title>
</head>

<body>

  <script src="https://app.coinpays.io/js/iframeResizer.min.js"></script>
    <iframe visible="false" runat="server" id="coinpaysiframe" frameborder="0" scrolling="no" style="width: 100%; "></iframe>
    <script>iFrameResize({}, '#coinpaysiframe');</script>

    </body>
</html>
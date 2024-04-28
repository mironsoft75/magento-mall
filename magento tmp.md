## Magento Command

```shell
/usr/local/bin/magepack

magepack bundle

magepack generate --cms-url="http://127.0.0.1/" --category-url="http://127.0.0.1/products.html" --product-url="http://127.0.0.1/products-smart-lights-bulbs-wl1000101-rgbw-us-4-w.html" --skip-checkout

magepack generate --cms-url="https://pre-aidot.arnoo.com/" --category-url="https://pre-aidot.arnoo.com/products.html" --product-url="https://pre-aidot.arnoo.com/products-smart-lights-bulbs-wl1000101-rgbw-us-4-w.html" --skip-checkout

php-cgi.exe -b 127.0.0.1:9002 -c php.ini

# run magento server
php -S 172.16.168.74:8082 -t ./pub/ ./phpserver/router.php

php bin/magento index:reindex

php -d memory_limit=-1 bin/magento setup:static-content:deploy --no-parent --area frontend -t Silk/aidot -f
php -d memory_limit=-1 bin/magento setup:static-content:deploy --no-parent --area adminhtml -f

rm -rf var/cache/ var/generation/ var/page_cache/ var/view_preprocessed/ generated/code/ generated/metadata/ pub/static/frontend && php -d memory_limit=-1 bin/magento setup:static-content:deploy --no-parent --area frontend -t Silk/aidot -f

cd ./pub/static/
find . -depth -name .htaccess -prune -o -delete

# pre server quick deploy
cd /data/website/aidot-m2/public_html && php bin/magento setup:di:compile && sh deploy_prod.sh

#
php bin/magento config:set dev/css/minify_files 1
php bin/magento config:set dev/css/merge_css_files 1
php bin/magento config:set dev/css/use_css_critical_path 1

php bin/magento config:set dev/js/minify_files 1
php bin/magento config:set dev/js/merge_files 1
php bin/magento config:set dev/js/enable_js_bundling 0
php bin/magento config:set dev/js/enable_magepack_js_bundling 1

php bin/magento config:set dev/template/minify_html 1



php -d memory_limit=-1 bin/magento setup:upgrade

php -d memory_limit=-1 bin/magento setup:di:compile

php -d memory_limit=-1 bin/magento setup:static-content:deploy -f

php -d memory_limit=-1 bin/magento indexer:reset && php -d memory_limit=-1 bin/magento indexer:reindex

# clean & flush cache
php -d memory_limit=-1 bin/magento cache:clean && php bin/magento cache:flush
```

## stripe
测试stripe支付账号：
https://stripe.com/docs/keys
https://stripe.com/docs/testing#cards
账号：4242424242424242
有效期：12/34
CVC：用任何三位数，比如：123
Name on card ：任意
Country or region : United States
code:12345
勾选：可无需勾选
点击支付：支付中... 支付成功后返回

测试公钥：pk_test_51KY2k0D7eF1VpsTopnjVcUENPTJbjINiu1uyeiXoOVLZKSmHHWMjp6JyqEARCvzBOOnChrBSDaLTt3Tlx5Y5RMbx00GP6ZDjEN
测试秘钥：sk_test_51KY2k0D7eF1VpsTowpwtK4OsJzM64vD2df8dHg2H2jKGhDUAr3zquJqKZicfEyp7g4x3I3fcmsV2fseGWomf0Aw100hvEXHh9x

## paypal
Paypal沙盒环境https://zhuanlan.zhihu.com/p/325685354
沙盒个人支付账号：
Email ID:
sb-hukc114546109@personal.example.com
System Generated Password:
PreAiDot@123
沙盒企业账号：
Username:
sb-8jxks14331379_api1.business.example.com
Password:
K8WMHG2DS4RHQW4S
Signature:
AdFhxPI4CVynL5srKXARC4WxEgPTAs9xyGAh0Goa3Pd3Qohnj6kpLmPr
Merchant Account ID：NE7CPDFQLF4N2
企业账号登录：https://www.sandbox.paypal.com/
登录账号：sb-8jxks14331379@business.example.com
登录密码：PreAiDot@123


# How to fix «file_put_contents(generated/metadata/primary|global|plugin-list.php): failed to open stream: No such file or directory in lib\internal\Magento\Framework\Interception\PluginListGenerator.php on line 414» in Magento 2.4-develop in Windows?
https://mage2.pro/t/topic/6178/3

magento/magento2/blob/49309635/lib/internal/Magento/Framework/Interception/PluginListGenerator.php#L158-L158
$cacheId = implode('|', $this->scopePriorityScheme) . "|" . $this->cacheId;
with the following one:
$cacheId = implode('-', $this->scopePriorityScheme) . "-" . $this->cacheId;


```bash
# Use Below Command for - Disable(0) / Enable(1) from SSH/Putty/CMD.

# Merge Minify JS

php bin/magento config:set dev/js/merge_files 0

php bin/magento config:set dev/js/minify_files 0

# JS Bundling

php bin/magento config:set dev/js/enable_js_bundling 0

# Merge Minify CSS

php bin/magento config:set dev/css/merge_css_files 0

php bin/magento config:set dev/css/minify_files 0

# CSS Critical Path

php bin/magento config:set dev/css/use_css_critical_path 0

# Minify HTML

php bin/magento config:set dev/template/minify_html 0

# Note: In last don't forget to run below command

php bin/magento cache:clean
```





```html
<!-- pre env -->
<meta name="google-site-verification" content="5PzErkoGn4v2P5e4wkIxgAorf-igf5LsiiuPQZ_ZnNs" />
<meta name="facebook-domain-verification" content="bldvoyqylm2bhll09yj38piwtb7t3s" />

<style>
.vc-toggle .vc-panel {bottom: 57px!important;}
</style>

<script src="https://cdn.jsdelivr.net/npm/vconsole@latest/dist/vconsole.min.js"></script>
<script>/(leedarson|android|iphone|ipad)/i.test(navigator.userAgent) && require(['VConsole'], function(v) {new v();})</script>

// Microsoft Clarity - Official
<script type="text/javascript">(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i+"?ref=gtm2";y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","hh57ulbkiu");</script>
```

normal: https://www.webpagetest.org/result/230510_BiDcN4_891/
bundle: https://www.webpagetest.org/result/230510_AiDc7Z_88C/
pure: https://www.webpagetest.org/result/230510_BiDc4Y_8N6/
lazyload： https://www.webpagetest.org/result/230510_AiDcJH_8FS/


```css
/** fix style */
.catalog-product-view .columns .column.main {order:unset !important;}
.swatch-attribute.size .swatch-option{background:#fff;color:#1f2429;}
.swatch-attribute.size .swatch-option.selected{background:#fff;border-color:#f29952;color:#fa7010;}
.products.wrapper .product-item .product-item-info .product-item-link {  word-break: break-all; }
```


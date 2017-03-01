
# Plugin para Opencart 2.3.x



## Descripción
Este modulo provee el servicio de ComproPago para poder generar intenciones de pago dentro de la plataforma Opencart.

Con ComproPago puede recibir pagos en OXXO, 7Eleven y muchas tiendas más en todo México.

[Registrarse en ComproPago ](https://compropago.com)


## Ayuda y Soporte de ComproPago

- [Centro de ayuda y soporte](https://compropago.com/ayuda-y-soporte)
- [Solicitar Integración](https://compropago.com/integracion)
- [Guía para Empezar a usar ComproPago](https://compropago.com/ayuda-y-soporte/como-comenzar-a-usar-compropago)
- [Información de Contacto](https://compropago.com/contacto)

Este modulo provee el servicio de ComproPago para poder generar intensiones de pago dentro de la plataforma Opencart.

* [Instalación](#install)
* [¿Cómo trabaja el modulo?](#howto)
* [Configuración](#setup)
* [Sincronización con los webhooks](#webhook)

## Requerimientos
* [Opencart 2.3.x +](https://www.opencart.com/)
* [PHP >= 5.4](http://www.php.net/)
* [PHP JSON extension](http://php.net/manual/en/book.json.php)
* [PHP cURL extension](http://php.net/manual/en/book.curl.php)
* [PHP mcrypt](http://php.net/manual/en/book.curl.php)

<a name="install"></a>
## Instalación:

1. Descarga la última versión del plugin de ComproPago para opencart desde la siguiente ruta [desde aquí](https://github.com/compropago/plugin-opencart-2.3/releases)

2. Descomprime el archivo ZIP, seguido ingresa a tu servidor mediante FTP y sube la carpeta "Upload" en el mismo directorio para cargar el módulo (Este proceso no replaza las carpetas y/o archivos existentes).

3. Ingresar al panel de opencart, seguido ir a *Extensions* -> *Extensions* -> Seleccionar el tipo de extensión *Payments* -> Click en el botón *instalar* de la columna *Acción* y finalmente dar click en el botón azul para entrar al módulo.

4. Ingresa a tu cuenta de ComproPago para copiar las llaves pública y privada de la sesión configuración y colocar los valores en la sección correspondiente.

<a name="howto"></a>
## ¿Cómo trabaja el modulo?
Una vez que el cliente sabe que comprar y continua con el proceso de compra entrará a la opción de elegir metodo de pago justo aqui aparece la opción de pagar con ComproPago<br /><br />

Una vez que el cliente completa su orden de compra iniciara el proceso para generar su intensión de pago, el cliente selecciona el establecimiento y recibe las instrucciones para realizar el pago.

Una vez que el cliente genero su intención de pago, dentro del panel de control de ComproPago la orden se muestra como "PENDIENTE" esto significa que el usuario esta por ir a hacer el deposito.


Una vez completado estos pasos el proceso de instalación queda completado.

### Documentación
### Documentación ComproPago Plugin Opencart

### Documentación de ComproPago
**[API de ComproPago] (https://compropago.com/documentacion/api)**

ComproPago te ofrece un API tipo REST para integrar pagos en efectivo en tu comercio electrónico o tus aplicaciones.


**[General] (https://compropago.com/documentacion)**

Información de Comisiones y Horarios, como Transferir tu dinero y la Seguridad que proporciona ComproPago


**[Herramientas] (https://compropago.com/documentacion/boton-pago)**
* Botón de pago
* Modo de pruebas/activo
* WebHooks
* Librerías y Plugins
* Shopify



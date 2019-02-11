# Presentación

Este complemento puede administrar muchas tiras de LED, bombillas LED o enchufes eléctricos controlados por wifi o radiofrecuencia de 2.4 GHz a través de una caja de wifi que se vende con el producto.


 ![ampoules](../images/icon0203.png) ![Prises](../images/icon1204.png) ![Bandeau led](../images/icon0500.png) ![Plafonnier](../images/icon0606.png)


# Compatibilidad y limitaciones.

## Productos compatibles

Hay muchos productos compatibles con el complemento, pero marcas muy diferentes o sin marca.

Productos compatibles:
-   Bombillas Mi.Light / EasyBulb / LimitlessLED: sin estado de retorno
-   Strip-led Mi.Light / EasyBulb / LimitlessLED: sin retroalimentación de estado
-   Controlador LED Mi.Light / EasyBulb / LimitlessLED V3.0 a V5.0: sin retroalimentación de estado
-   Controlador LED Mi.Light / EasyBulb / LimitlessLED V6.0 / iBox1 / iBox2: sin retroalimentación de estado
-   Blanco y color Xiaomi Yeelight bombillas WiFi con retroalimentación de estado
-   Strip-led WiFi Xiaomi Yeelight color con retroalimentación de estado
-   Luz de techo WiFi Xiaomi Yeelight con retorno de estado
-   Lámpara de escritorio WiFi Xiaomi Mijia con retroalimentación de estado

Productos que pueden ser compatibles y sin garantía:
-   LW12 / Lagute: controlador de tira led RGB: estado de retorno
-   Controlador de tira led Wifi 320/370 RGB / RGBW: retorno de estado parcial
-   Magic UFO: RGBW strip-led controller, manijas blancas
-   MagicHome: controlador de tiras RGBW / RGBWW y bombillas / puntos RGBW compatibles con la aplicación MagicHome
-   H801: controlador de tira led RGBW, sin estado de retorno
-   Arilux AL-C01 / 02/03/04/06/10/10: controlador de tira led RGB / RGBW / RGBWW, retroalimentación de estado
-   TP-Link LB100 / 110/120/130: bombillas con retroalimentación de estado
-   Lámpara Extel Meli con retroalimentación de estado.
-   Xiaomi Philips: Lámpara de escritorio, lámpara y lámpara de techo con retroalimentación de estado
-   Bombillas compatibles Tuya Smart live o Jinvoo smart o eFamilyCloud apps con comentarios de estado
-   Tuya Smart live o Jinvoo tomas de corriente compatibles inteligentes o aplicaciones eFamilyCloud con comentarios de estado
-   Tomacorrientes TP-link HS100 HS110 con retroalimentación de estado
-   Controladores de tiras de píxeles LED compatibles con Magic Home con retroalimentación de estado

Para estos controladores, bombillas o enchufes, el protocolo de intercambio no proviene directamente del fabricante, que puede cambiarlo en cualquier momento. Existen varias versiones en el mercado que no son todas compatibles con el complemento.

Productos incompatibles y que no serán:
-   Bombillas, tomas de corriente o controladores que contienen un receptor bluetoooth en lugar de un receptor de radio de 2.4 Ghz o WiFi.
-   Los controladores de tira de LED o bombillas y bombillas o tomas de corriente que utilizan una conexión WiFi punto a punto con la aplicación móvil.
-   La lámpara de cabecera Xiaomi no es compatible (por construcción).

## Prueba de compatibilidad

A petición, se puede proporcionar un complemento de prueba.

Es recomendable preguntar en el foro para conocer la compatibilidad de un producto poco difundido.

Vaya al foro de Jeedom aquí: <https://www.jeedom.com/forum/viewtopic.php?f=28&amp;t=24322>


# limitaciones

Mi.Light / EasyBulb / LimitlessLED:
-   Todas las características son tenidas en cuenta por el complemento.

LW12 / Lagute:
-   La programación de los modos personalizados no es posible, debe utilizar la aplicación provista con el controlador (Magic Home). Por otro lado, los modos personalizados pueden activarse con el complemento.
-   Existen varias versiones de LW12 que pueden no ser compatibles con el complemento.

Magic UFO, MagicHome y Arilux AL-C01 / 02/03/04/06/10:
-   La programación en modo personalizado, el modo de radio y los temporizadores no son compatibles. Es necesario utilizar la aplicación provista con el controlador (Magic Home). Por otro lado, los modos personalizados pueden activarse con el complemento.
-   Existen diferentes modelos que pueden no ser compatibles con el complemento.

Xiaomi Yeelight:
-   Los comandos HSV no son compatibles. El flujo y la escena se crean al crear comandos con el código JSON correspondiente al efecto deseado (consulte la documentación de la API de YeeLight).
-   La lámpara de cabecera Xiaomi no es compatible.
-   La lámpara de escritorio Xiaomi Mijia es parcialmente compatible (no hay comentarios de estado completos).

Wifi 320/370:
-   El estado de los modos de escena no se gestiona, solo se gestiona ON / OFF.
-   Existen diferentes modelos que pueden no ser compatibles con el complemento.

H810:
-   Los juegos de escenario no son gestionados.
-   Existen diferentes modelos que pueden no ser compatibles con el complemento.

TP-Link:
-   Los temporizadores no se gestionan.
-   La información de consumo de energía no es compatible con las bombillas.

Extel Meli:
-   La parte de sonido de la lámpara no se tiene en cuenta.

Xiaomi Philips:
-   Todas las características se tienen en cuenta.

Bombillas o enchufes compatibles Aplicaciones Tuya Smart live o Jinvoo smart o eFamilyCloud:
-   Todas las características se tienen en cuenta.

Controladores de píxeles de píxeles compatibles con Magic Home:
-   Las escenas personalizadas no son compatibles.

# Configuración del módulo wifi

## Instala los leds

Descargue la aplicación móvil del fabricante y siga las instrucciones para controlar los leds con el móvil. Para cada dispositivo wifilightV2, se proporciona ayuda detallada en la página de configuración.

Mientras la lámpara no esté controlada con la aplicación móvil, el complemento no funcionará.

Consulte la ayuda y los foros del fabricante de las lámparas.


## Configurar el enrutador
Es necesario configurar el DHCP de su enrutador (generalmente proporcionado por su proveedor de servicios) para modificar la atribución de la dirección IP del módulo wifi o la bombilla o la captura para que sea estática. Califica esta dirección. En general, será de la forma:
192.168.1.xxx
donde xxx es la dirección del módulo wifi (2 a 254)

Revise los foros en su casilla para aprender cómo configurar su DHCP.

Después de este cambio, verifique que la aplicación móvil aún esté controlando las lámparas.

Luego, puede cambiar a la configuración del complemento wifilightV2.

## Instalación y configuración de plugins

Ayuda :
-   Utilice el icono de signo de interrogación para obtener ayuda en cada elemento de configuración.

Ajuste:
-   Para configurar un dispositivo, elija el menú Complementos / Comunicar objetos / wifilightV2
-   Luego haga clic en el botón en la parte superior izquierda Agregar un módulo Wifi
-   Introduce el nombre del módulo wifi.
-   Ingrese el objeto padre
-   Elija la categoría Luz (por defecto)
-   Habilitar y hacer visible (por defecto)
-   Ingrese la dirección IP del módulo del zócalo WiFi o la bombilla (consulte las Preguntas frecuentes para obtener más información)
-   Para algunos dispositivos se solicita ingresar al canal utilizado, crear un dispositivo wifilightV2 por canal
-   Para algunos dispositivos se solicita ingresar un token o (y) un identificador, consulte la ayuda en la página de configuración del dispositivo
-   Para algunos controladores es necesario indicar el número de leds de la tira de píxeles leds
-   Para algunos controladores es necesario indicar el orden de los colores si los colores por defecto no corresponden
-   Introduzca la marca o categoría de la lámpara, tira de led o puente.
-   Ingrese el tipo exacto de controlador, bombilla, enchufe o tira led, esto es esencial para crear los comandos para controlar el dispositivo
-   Ingrese el número de despachos de comando: le permite repetir el comando para un dispositivo remoto en caso de una transmisión incorrecta. (1 por defecto). Algunas bombillas o enchufes no gestionan esta repetición porque el complemento garantiza el retorno del estado de la transmisión. Algunos comandos relativos (incrementos) no se repiten.
-   Introduzca la demora de envío en caso de repetición (0 ms por defecto, 100 ms como máximo)
-   Ingrese el% de incremento de intensidad al presionar los botones para aumentar o disminuir la intensidad de la luz
-   Ingrese el número de grupo para la sincronización, vea a continuación

## Añadiendo comandos
Al guardar el módulo, los comandos se crean automáticamente.

El nombre de los comandos puede ser cambiado. Los comandos creados y eliminados automáticamente se recrean durante una copia de seguridad.

Cuando se crean todos los comandos, pueden sobrecargar la interfaz, es posible que no se muestren configurando el comando.

## Modificación de categoría o modelo de bulbo, casquillo o tira.

-   eliminar todos los pedidos
-   cambiar la categoría o modelo
-   ahorra 2 veces

# Operación de retroalimentación de estado y estado de conexión

## Compatibilidad del retorno estatal.

El feedback de estado funciona con los controladores LW12 / Lagute, Magic UFO, Arilux y Wifi 3x0 (parcialmente), así como con las bombillas y la diadema Xiaomi YeeLight, las bombillas y las tomas TP-Link, las bombillas, las bombillas y las tomas Philips Xiaomi. Aplicación inteligente compatible con Tuya, Extel Meli y los controladores de tiras de píxeles compatibles con Magic Home.

## principio

El retorno del estado consiste en que Jeedom recupere el estado del controlador si su estado ha sido cambiado por otro maestro distinto de Jeedom: aplicación portátil o control remoto.

## Actualización periódica de Jeedom
LW12 / Lagute, Magic UFO, Arilux, Wifi 3x0, TP-Link, Extel Meli, Xiaomi Yeelight, Philips Xiaomi, Tuya / Jinvoo / eFamillyCloud aplicaciones y controladores de píxeles leds hogar mágico compatible: cada minuto Jeedom interroga al controlador, la toma o la bombilla para conocer su estado y actualizar el ritmo de los widgets del complemento (controles deslizantes y color). La información correspondiente al estado se actualiza y puede ser consultada por los escenarios.

## Actualizar por escenario

Los comandos xxxxGet y Status se pueden usar en un escenario Jeedom.

## Información de conexión:

El comando ConnectedGet recupera el estado de conexión de cada dispositivo. Se actualiza cada minuto.
-  1: dispositivo con retroalimentación de estado OK
-  2: No se puede preparar la conexión del dispositivo
-  3: dispositivo no conectado
-  4: no hay respuesta del dispositivo
-  5: respuesta incorrecta del dispositivo
-  6: dispositivo sin retroalimentación de estado

# Cómo funciona la sincronización

##  Principio de sincronizacion

Es posible sincronizar varios bulbos o tomados de diferentes marcas:

Todas las bombillas o tomas que tienen el mismo número de grupo están sincronizadas

El grupo 0 no está sincronizado (grupo predeterminado)

Cuando se utiliza un control de una bombilla o un enchufe del grupo, el mismo comando se aplica a todas las bombillas del mismo grupo

Si el comando no existe para la bombilla o el zócalo sincronizado, simplemente se ignora.

Atención, las lámparas no se ordenarán exactamente al mismo tiempo porque la latencia se retrasa al enviar pedidos que se realizan uno después del otro.

## Configuracion de sincronizacion

Simplemente coloque un número diferente de cero en el campo de grupo cuando configure el equipo. Todos los equipos con los mismos números serán sincronizados.

# Caja especial de cajas Mi.Light.

## Configurando iBox 1 o 2

Desde la versión 1.0.58 de iBox 1 y 2, es esencial modificar su configuración para que puedan dialogar con Jeedom.

Conéctese a http (con un navegador web) a la dirección IP de su iBox. Los identificadores predeterminados son admin / admin. Vaya a la pestaña "Otra configuración" y en "Configuración de parámetros de red / Protocolo" elija UDP y guarde.

# Caso especial de Xiaomi Yeelight.

## Configuración de la bombilla
Es esencial habilitar el control de LAN a través de la aplicación Xiaomi Yeelight.

## Modo de escena Xiaomi Yeelight
Es posible configurar los modos de escena. Varios modos de escena están preprogramados en el complemento, pero es posible agregar otros modos de escena.

Basta con respetar ciertas condiciones:
-   Agrega un comando de acción wifilightV2 de tipo Predeterminado
-   Dale un nombre (por ejemplo, Scene Blink)
-   En los parámetros, configure el comando de escena Yeelight, por ejemplo: "id": 1, "method": "set_scene", "params": ["cf", 0,0, "500,1,255,100,1000,1,16776960, 70 "]

No coloque las llaves de inicio y fin, así como los caracteres de retorno a la línea, el complemento los agregará automáticamente
Inspire los comandos preconfigurados para crear estos modos de escena adicionales.

## Actualización del estado de la lámpara en Jeedom.
Al activar el complemento y tan pronto como se inicia el demonio, así como cada 5 minutos, el complemento busca las bombillas encendidas y conectadas a Jeedom.

Tan pronto como se encuentra la bombilla, el estado de la bombilla se vuelve a ensamblar al complemento inmediatamente.

Tenga en cuenta que el complemento puede tardar hasta 5 minutos en encontrar una bombilla.

# Caso especial de Philips Xiaomi.

## Configuración de la bombilla

Es esencial recuperar un token que permita que el complemento interactúe con los dispositivos Philips Xiaomi.

El procedimiento es complejo y requiere varias manipulaciones. Realice una búsqueda en la web con la siguiente palabra clave: token de Xiaomi.

No se dará ninguna ayuda para recuperar el token.

# Caso especial de bombillas y tomas compatibles Tuya / Smart live / Jinvoo / eFamillyCloud apps

## Bombilla de luz o configuración de zócalo.

Es esencial recuperar una clave local (LocalKey) y un identificador que permita que el complemento interactúe con estas bombillas o enchufes.

El procedimiento es complejo y requiere varias manipulaciones. Haga su búsqueda en la web con la palabra clave: Tuya localkey, en Github en particular.

La bombilla o el zócalo no deben estar conectados a una aplicación en el teléfono, de lo contrario no responderán a los pedidos de Jeedom. Por lo tanto, es necesario cerrar cualquier aplicación posiblemente conectada con la bombilla o el enchufe.

Si la bombilla o el zócalo se desinstalan y se vuelven a instalar en la aplicación móvil, se cambiará su clave. Será necesario recuperar la clave con el procedimiento anterior.
No se dará ninguna ayuda para recuperar la clave o el identificador.

# Preguntas frecuentes


## ¿Qué bombillas o enchufe se pueden utilizar?

lee la documentación

## No pasa nada

Primero ejecute las lámparas con la aplicación móvil provista por el fabricante.

Use el botón <test> en el menú Complementos / Objetos conectados / wifilightV2 /.

No se proporcionará ayuda sin que las lámparas estén operativas con la aplicación de lámpara del fabricante en un teléfono móvil.
Es necesario dar una dirección IP fija al controlador o a la lámpara.


## No sé cómo configurar mi caja de internet.

No se proporcionará ayuda en la caja y los conceptos necesarios para configurar el enrutador para asignar una dirección IP fija. Consulta los foros de la caja.

## No se crean todos los pedidos al cambiar un modelo de bombilla o de salida

Ahorra 2 veces.

## El manejo de la intensidad de la luz blanca Mi.Light / EasyBulb / LimitlessLED no es práctico

El fabricante de LED no tiene previsto afectar directamente la intensidad de la bombilla. Solo podemos incrementar o disminuir desde el valor anterior. El complemento solo replica esta operación. El cursor que se propone es por tanto caprichoso.

## El manejo de la intensidad del color a veces tiene comportamientos inesperados.

Ningún protocolo maneja la intensidad del color, aunque generalmente las aplicaciones móviles lo hacen. Mientras Jeedom maneja el color y la intensidad, todo va bien. Pero si la intensidad es modificada por una aplicación móvil, los resultados no siempre son los esperados. El complemento intenta corregir el problema cuando la lámpara o el controlador tienen una respuesta.

## ¿Hay un retorno de estado?

Lee la documentacion

## No se pueden operar las bombillas Xiaomi Yeelight.

Es esencial activar el modo de control de LAN a través de la aplicación Xiaomi Yeelight.

## No controlo el sonido de las bombillas extel meli.

El sonido no es compatible con el complemento

## No se pueden utilizar las bombillas Philips Xiaomi.

Para interactuar con las bombillas Philips Xiaomi, es necesario transmitir un token o token en inglés. Sin este token, la bombilla no tendrá en cuenta los pedidos que se le envíen. Este token se encuentra en la aplicación Mi-Home y, dependiendo de su teléfono, hay varias formas de recuperar el token. El procedimiento se describe en varios sitios, pero no se reproduce aquí por dos razones principales:

-   Xiaomi ya ha modificado su protocolo que obligó a modificar el procedimiento para recuperar el token, aún podría hacerlo.
-   Nuevos procedimientos más simples pueden estar disponibles para los usuarios de Internet.
-   Esta documentación no se mantendrá tan rápido como una simple búsqueda en la web con las palabras clave: token xiaomi.

## Incapaz de operar bombillas o enchufes compatibles Aplicaciones Tuya / Smart live / Jinvoo / eFamillyCloud

Para interactuar con estas bombillas y enchufes, es necesario transmitir una clave local o Localkey o token en inglés y un identificador. Sin estos parámetros, la bombilla no tendrá en cuenta los pedidos que se le envíen. Hay varios métodos para recuperar esta información. El procedimiento se describe en varios sitios, pero no se reproduce aquí por dos razones principales:

-   Las aplicaciones se han actualizado, lo que ha significado cambiar el procedimiento para recuperar la información.
-   Nuevos procedimientos más simples pueden estar disponibles para los usuarios de Internet.
-   Esta documentación no se mantendrá tan rápido como una simple búsqueda en la web con las palabras clave: Tuya LocalKey y, en particular, en Github.

# ¿Cómo conseguir ayuda?
Vaya al foro de Jeedom aquí: <https://forum.jeedom.fr/viewtopic.php?f=28&amp;t=2840>

# dificultades

Error al enviar el comando / rueda dentada sin parada / Emisión sin parada

-   Los dispositivos wifilightV2 necesitan ser actualizados
-   Entra en cada equipo y ahorra 2 veces.
-   Prueba con nuevos equipos si esto persiste.

Mi.Light bridge IBox1, iBox2, V6: comando tomado en cuenta al azar
-   los pedidos se envían demasiado rápido
-   En escenarios, poner descansos de suficiente duración.

Mi.Light bridge IBox1, iBox2, V6: comando no tomado en cuenta
-   al vincularse con la aplicación móvil Mi.Light y al configurar wifilightV2, elija el mismo control remoto.

LW12 UFO Magic Arilux Wifi3x0 H801 Compatible con Magic Home Tuya / Jinvoo / eFamillyCloud Controllers
-   muchas versiones existentes y algunas pueden ser incompatibles con el complemento. Póngase en contacto con el autor para una posible actualización.

Faltan órdenes al crear o cambiar el subtipo de la bombilla
-   guardar el equipo (2 veces)



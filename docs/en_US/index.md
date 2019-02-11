# Presentation

This plugin can manage many LED strips or LED bulbs or electrical plugs controlled by wifi or radio frequency 2.4 GHz via a box wifi sold with the product.


 ![ampoules](../images/icon0203.png) ![Prises](../images/icon1204.png) ![Bandeau led](../images/icon0500.png) ![Plafonnier](../images/icon0606.png)


# Compatibility and limitations

## Compatible products

There are many products compatible with the plugin but very different brands or brands.

Compatible products:
-   Light bulbs Mi.Light / EasyBulb / LimitlessLED: no return status
-   Strip-led Mi.Light / EasyBulb / LimitlessLED: no status feedback
-   Mi.Light / EasyBulb / LimitlessLED LED Controller V3.0 to V5.0: no status feedback
-   Mi.Light / EasyBulb / LimitlessLED V6.0 / iBox1 / iBox2 led controller: no status feedback
-   White and color Xiaomi Yeelight WiFi bulbs with status feedback
-   Strip-led WiFi Xiaomi Yeelight color with status feedback
-   Xiaomi Yeelight WiFi Ceiling Light with Status Return
-   Xiaomi Mijia WiFi Desk Lamp with Status Return

Products that can be compatible and unsecured:
-   LW12 / Lagute: RGB strip-led controller: return status
-   Wifi 320/370 RGB / RGBW strip-led controller: partial state return
-   Magic UFO: RGBW strip-led controller, handles white
-   MagicHome: RGBW / RGBWW strip-led controller and bulbs / spots RGBW compatible with the MagicHome app
-   H801: RGBW strip-led controller, no return status
-   Arilux AL-C01 / 02/03/04/06/10: RGB / RGBW / RGBWW strip-led controller, status feedback
-   TP-Link LB100 / 110/120/130: bulbs with status feedback
-   Extel Meli lamp with status feedback
-   Xiaomi Philips: Desk lamp, bulb and ceiling lamp with status feedback
-   Compatible bulbs Tuya Smart live or Jinvoo smart or eFamilyCloud apps with status feedback
-   Tuya Smart live or Jinvoo smart compatible power outlets or eFamilyCloud apps with status feedback
-   TP-link HS100 HS110 power outlets with status feedback
-   Magic home compatible LED pixel strip controllers with status feedback

For these controllers, bulbs or sockets, the exchange protocol does not come directly from the manufacturer who can change it at any time. Several versions exist on the market that are not all compatible with the plugin.

Incompatible products and who will not be:
-   Led bulbs, sockets or controllers that contain a bluetoooth receiver instead of a 2.4 Ghz radio receiver or WiFi.
-   The led strip controllers or bulbs and bulbs or sockets that use a point-to-point WiFi connection with the mobile application.
-   The Xiaomi bedside lamp is not compatible (by construction).

## Compatibility test

On request, a test plugin can be provided.

It is advisable to inquire on the forum to know the compatibility of a product little diffused.

Go to Jeedom's forum here: <https://www.jeedom.com/forum/viewtopic.php?f=28&amp;t=24322>


# limitations

Mi.Light / EasyBulb / LimitlessLED:
-   All features are taken into account by the plugin.

LW12 / Lagute:
-   Programming custom modes is not possible, it is necessary to use the application provided with the controller (Magic Home). On the other hand, custom modes can be triggered with the plugin.
-   There are several versions of LW12 that may not be compatible with the plugin.

Magic UFO, MagicHome and Arilux AL-C01 / 02/03/04/06/10:
-   Custom mode programming, radio mode and timers are not supported. You must use the application supplied with the controller (Magic Home). On the other hand, custom modes can be triggered with the plugin.
-   There are different models that may not be compatible with the plugin.

Xiaomi Yeelight:
-   HSV commands are not supported. Flow and scene are created by creating commands with the JSON code corresponding to the desired effect (see the YeeLight API documentation).
-   The Xiaomi bedside lamp is not compatible.
-   The Xiaomi Mijia desk lamp is partially compatible (no full status feedback).

Wifi 320/370:
-   The status of the scene modes is not managed, only ON / OFF is managed.
-   There are different models that may not be compatible with the plugin.

H810:
-   stage games are not managed.
-   There are different models that may not be compatible with the plugin.

TP-Link:
-   Timers are not managed.
-   Power consumption information is not supported for light bulbs.

Extel Meli:
-   The sound part of the lamp is not taken into account

Xiaomi Philips:
-   All features are taken into account

Compatible bulbs or plugs Tuya Smart live or Jinvoo smart or eFamilyCloud apps:
-   All features are taken into account

Magic Home compatible pixel pixel controllers:
-   custom scenes are not supported.

# WiFi module configuration

## Install the leds

Download the mobile application of the manufacturer and follow the instructions to control the leds with the mobile. For each wifilightV2 device, detailed help is provided in the configuration page.

As long as the lamp is not controlled with the mobile application, the plugin will not work.

Consult the help and the forums of the manufacturer of the lamps.


## Configure the router
It is necessary to configure the DHCP of your router (generally provided by your service provider) to modify the attribution of the IP address of the module wifi or the bulb or the catch so that it is static. Rate this address. In general, it will be of the form:
192.168.1.xxx
where xxx is the address of the wifi module (2 to 254)

Check the forums in your box to learn how to configure your DHCP.

After this change, verify that the mobile app is still controlling the lamps.

You can then go to the configuration of the wifilightV2 plugin.

## Plugin Installation and Configuration

Help :
-   Use the question mark icon for help on each configuration item.

Setting :
-   To set up a device, choose the menu Plugins / Communicating objects / wifilightV2
-   Then click on the button at the top left Add a Wifi module
-   Enter the name of the wifi module
-   Enter the parent object
-   Choose the category Light (default)
-   Enable and make visible (default)
-   Enter the IP address of the module of the WiFi socket or light bulb (see the FAQ for more information)
-   For some devices it is requested to enter the channel used, create a device wifilightV2 per channel
-   For some devices it is requested to enter a token or (and) an identifier, consult the help on the device configuration page
-   For some controllers it is necessary to indicate the number of leds of the pixel strip leds
-   For some controllers it is necessary to indicate the order of the colors if the colors by default do not correspond
-   Enter the brand or category of the lamp, strip-led or bridge
-   Enter the exact type of controller, bulb, plug or strip-led, this is essential to create the commands to control the device
-   Enter the number of command dispatches: allows you to repeat the command for a remote device in the event of a bad transmission. (1 by default). Some bulbs or plugs do not manage this repetition because the plugin ensures by return of state of the transmission. Some relative commands (increments) are not repeated.
-   Enter the sending delay in case of repetition (default 0 ms, 100 ms max)
-   Enter the% intensity incrementaion when pressing the buttons for incrementing or decrementing the light intensity
-   Enter the group number for synchronization, see below

## Adding commands
when saving the module, the commands are automatically created.

the name of the commands can be changed. The commands automatically created and deleted are recreated during a backup.

when all the commands are created, they can weigh down the interface, it is possible not to display them by configuring the command.

## Modification of category or model of bulb, socket or strip

-   remove all orders
-   change the category or model
-   save 2 times

# Operation of state feedback and connection status

## Compatibility of the state return

The status feedback works with the LW12 / Lagute, Magic UFO, Arilux and Wifi 3x0 controllers (partially) as well as Xiaomi YeeLight bulbs and headband, TP-Link bulbs and sockets, Philips Xiaomi bulbs, bulbs and sockets. Tuya compatible smart app, Extel Meli and the Magic Home compatible pixel strip controllers.

## Principle

The return of state consists in Jeedom recovering the state of the controller if its state has been changed by another master than Jeedom: portable app or remote control.

## Periodic update of Jeedom
LW12 / Lagute, Magic UFO, Arilux, Wifi 3x0, TP-Link, Extel Meli, Xiaomi Yeelight, Philips Xiaomi, Tuya / Jinvoo / eFamillyCloud apps and pixel controllers leds compatible magic home: every minute Jeedom interrogates the controller, the socket or the bulb to know its status and update the pace of the widgets of the plugin (sliders and color). Information corresponding to the state is updated and can be queried by the scenarios.

## Update by scenario

The xxxxGet and Status commands can be used in a Jeedom scenario.

## Connection Information:

The ConnectedGet command retrieves the connection status of each device. It is updated every minute.
-  1: device with status feedback OK
-  2: Can not prepare device connection
-  3: device not connected
-  4: no response from the device
-  5: wrong device response
-  6: device without status feedback

# How synchronization works

##  Principle of synchronization

It is possible to synchronize several bulbs or taken from different brands:

All bulbs or jacks that have the same group number are synchronized

Group 0 is not synchronized (default group)

When using a control of a bulb or a plug of the group, the same command is applied on all the bulbs of the same group

If the command does not exist for the light bulb or synchronized socket, it is simply ignored.

Attention, the lamps will not be ordered exactly at the same time because latency delays when sending orders that are done one after the other.

## Synchronization configuration

Simply put a different number of zero in the group field when configuring the equipment. All equipment with the same numbers will be synchronized.

# Special case of Mi.Light boxes

## Configuring iBox 1 or 2

Since version 1.0.58 of iBox 1 and 2, it is essential to modify their configuration so that they can dialogue with Jeedom.

Connect to http (with a web browser) to the IP address of your iBox. The default identifiers are admin / admin. Go to the "Other Setting" tab and in "Network Parameters setting / Protocol" choose UDP and save.

# Special case of Xiaomi Yeelight

## Bulb configuration
It is essential to enable LAN control via the Xiaomi Yeelight application.

## Xiaomi Yeelight Scene Mode
It is possible to configure the scene modes. Several scene modes are preprogrammed in the plugin but it is possible to add other scene modes.

It suffices to respect certain conditions:
-   Add a wifilightV2 action command of type Default
-   Give it a name (eg Scene Blink)
-   In parameters, set the scene command Yeelight, for example: "id": 1, "method": "set_scene", "params": ["cf", 0,0, "500,1,255,100,1000,1,16776960, 70 "]

Do not put the start and end braces as well as the return characters to the line, the plugin will add them automatically
Inspire preconfigured commands to create these additional scene modes.

## Update of the lamp status in Jeedom
When activating the plugin and as soon as the daemon is launched as well as every 5 minutes, the plugin searches for the bulbs powered and connected to Jeedom.

As soon as the bulb is found, the state of the bulb is reassembled to the plugin immediately.

Note that the plugin can take up to 5 minutes to find a light bulb.

# Special case of Philips Xiaomi

## Bulb configuration

It is essential to recover a token allowing the plugin to interact with Philips Xiaomi devices.

The procedure is complex and requires several manipulations. Do a search on the web with the following keyword: Xiaomi token.

No help will be given to recover the token.

# Special case of compatible bulbs and jacks Tuya / Smart live / Jinvoo / eFamillyCloud apps

## Light bulb or socket configuration

It is essential to recover a local key (LocalKey) and an identifier allowing the plugin to interact with these bulbs or plugs.

The procedure is complex and requires several manipulations. Do your web search with keyword: Tuya localkey, on Github in particular.

The bulb or socket must not be connected to an application on the phone, otherwise they will not respond to Jeedom's orders. It is therefore necessary to close any application possibly connected with the bulb or the plug.

If the light bulb or socket is uninstalled and reinstalled in mobile applciation, then its key will be changed. It will be necessary to retrieve the key with the procedure above.
No help will be given to recover the key or the identifier.

# FAQ


## Which bulbs or plug can be used?

read the documentation

## Nothing is happening

First run the lamps with the mobile application provided by the manufacturer.

Use the <test> button in the Plugin / Connected Objects / wifilightV2 / commands menu.

No help will be provided without the lamps being operational with the manufacturer's lamp application on a mobile phone.
It is necessary to give a fixed IP address to the controller or the lamp.


## I do not know how to configure my internet box

No help will be provided on the box and the concepts necessary to configure the router to assign a fixed IP address. Consult the forums of the box.

## All orders are not created when changing a bulb or outlet model

Save 2 times.

## White light intensity management Mi.Light / EasyBulb / LimitlessLED is not practical

The manufacturer of LEDs has not planned to directly affect the intensity of the bulb. We can only increment or decrement from the previous value. The plugin only replicates this operation. The cursor that is proposed is therefore capricious.

## Color intensity management sometimes has unexpected behaviors

No protocol handles the intensity of color, although usually mobile apps do. As long as Jeedom manages color and intensity, everything goes well. But if the intensity is modified by a mobile applciation, the results are not always those expected. The plugin tries to correct the problem when the lamp or the controller has a feedback.

## Is there a return of state?

Read the documentation

## Can not operate Xiaomi Yeelight bulbs

It is essential to activate the LAN control mode via the Xiaomi Yeelight application.

## I do not control the sound of Extel Meli bulbs

Sound is not supported by the plugin

## Can not operate Philips Xiaomi bulbs

To interact with Philips Xiaomi bulbs, it is necessary to transmit a token or token in English. Without this token, the bulb will not take into account the orders sent to it. This token is in the Mi-Home app and, depending on your phone, there are several ways to recover the token. The procedure is described on several sites but it is not reproduced here for two main reasons:

-   Xiaomi has already modified its protocol which has forced to modify the procedure to recover the token, it could still do it.
-   New, simpler procedures can be made available to Internet users.
-   This documentation will not be maintained as quickly as a simple search on the web with the keywords: xiaomi token.

## Unable to operate compatible bulbs or plugs Tuya / Smart live / Jinvoo / eFamillyCloud apps

To interact with these bulbs and sockets, it is necessary to transmit a local key or Localkey or token in English and an identifier. Without these parameters, the bulb will not take into account the orders sent to it. There are several methods to retrieve this information. The procedure is described on several sites but it is not reproduced here for two main reasons:

-   The applications have been updated, which has meant changing the procedure to retrieve the information.
-   New, simpler procedures can be made available to Internet users.
-   This documentation will not be maintained as quickly as a simple search on the web with the keywords: Tuya LocalKey and in particular on Github.

# How to get help?
Go to Jeedom's forum here: <https://forum.jeedom.fr/viewtopic.php?f=28&amp;t=2840>

# difficulties

Error sending command / notched wheel without stop / Emission without stop

-   wifilightV2 devices need to be updated
-   go into each equipment and save 2 times
-   test with new equipment if this persists

Mi.Light bridge IBox1, iBox2, V6: command taken into account randomly
-   orders are sent too quickly
-   in scenarios, put breaks of sufficient duration

Mi.Light bridge IBox1, iBox2, V6: command not taken into account
-   when pairing with the Mi.Light mobile application and when configuring wifilightV2, choose the same remote control.

LW12 UFO Magic Arilux Wifi3x0 H801 Compatible Magic Home Tuya / Jinvoo / eFamillyCloud Controllers
-   many existing versions and some may be incompatible with the plugin. Contact the author for a possible update.

Orders are missing when creating or changing the subtype of the bulb
-   save the equipment (2 times)



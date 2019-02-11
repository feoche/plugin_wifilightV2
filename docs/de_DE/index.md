# Präsentation

Dieses Plugin kann viele LED-Streifen oder LED-Lampen oder elektrische Stecker verwalten, die über WLAN oder Radiofrequenz 2,4 GHz gesteuert werden, über eine WLAN-Box, die im Lieferumfang des Produkts enthalten ist.


 ![ampoules](../images/icon0203.png) ![Prises](../images/icon1204.png) ![Bandeau led](../images/icon0500.png) ![Plafonnier](../images/icon0606.png)


# Kompatibilität und Einschränkungen

## Kompatible Produkte

Es gibt viele Produkte, die mit dem Plugin kompatibel sind, aber Marken, die sehr unterschiedlich sind oder keine Marken enthalten.

Kompatible Produkte:
-   Glühlampen Mi.Light / EasyBulb / LimitlessLED: Kein Rückgabestatus
-   LED-Leuchte Mi.Light / EasyBulb / LimitlessLED: Keine Statusrückmeldung
-   Mi.Light / EasyBulb / LimitlessLED LED-Controller V3.0 bis V5.0: Keine Statusrückmeldung
-   Mi.Light / EasyBulb / LimitlessLED LED-Controller V6.0 / iBox1 / iBox2: keine Statusrückmeldung
-   Weiße und farbige Xiaomi Yeelight WiFi-Lampen mit Statusrückmeldung
-   Streifengeführte WiFi Xiaomi Yeelight-Farbe mit Statusrückmeldung
-   Xiaomi Yeelight WiFi Deckenleuchte mit Status Return
-   WiFi-Tischlampe Xiaomi Mijia mit Statusrückmeldung

Produkte, die kompatibel und ungesichert sein können:
-   LW12 / Lagute: RGB-Streifen-LED-Controller: Rückgabestatus
-   Streifen-LED-Controller Wifi 320/370 RGB / RGBW: Teilzustandsrückgabe
-   Magisches UFO: RGBW-Streifensteuergerät, Griffe weiß
-   MagicHome: RGBW / RGBWW-Controller und RGBW-kompatible Glühlampen / Spots, die mit der MagicHome-App kompatibel sind
-   H801: RGBW-Streifen-Controller, kein Rückgabestatus
-   Arilux AL-C01 / 02/03/04/06/10: RGB / RGBW / RGBWW-LED-Controller mit Statusanzeige
-   TP-Link LB100 / 110/120/130: Lampen mit Statusrückmeldung
-   Extel Meli Lampe mit Statusrückmeldung
-   Xiaomi Philips: Schreibtischlampe, Glühlampe und Deckenleuchte mit Statusrückmeldung
-   Kompatible Glühbirnen Tuya Smart Live oder Jinvoo Smart oder eFamilyCloud-Apps mit Statusrückmeldung
-   Tuya Smart Live oder Jinvoo Smart kompatible Steckdosen oder eFamilyCloud-Apps mit Statusrückmeldung
-   TP-Link HS100 HS110 Steckdosen mit Statusrückmeldung
-   Magic Home-kompatible LED-Pixelstreifen-Controller mit Statusrückmeldung

Bei diesen Steuerungen, Glühlampen oder Sockeln stammt das Austauschprotokoll nicht direkt vom Hersteller, der es jederzeit ändern kann. Es gibt mehrere Versionen auf dem Markt, die nicht alle mit dem Plugin kompatibel sind.

Inkompatible Produkte und wer wird nicht sein:
-   LED-Lampen, Steckdosen oder Controller, die einen Bluetooth-Empfänger anstelle eines 2,4-GHz-Funkempfängers oder WLAN enthalten
-   Die LED-Streifensteuerungen oder -lampen und -lampen oder -fassungen, die eine Punkt-zu-Punkt-WLAN-Verbindung mit der mobilen Anwendung verwenden
-   Die Xiaomi Nachttischlampe ist nicht kompatibel (konstruktiv).

## Kompatibilitätstest

Auf Wunsch kann ein Test-Plugin bereitgestellt werden.

Es ist ratsam, sich im Forum anzufragen, um die Kompatibilität eines Produkts zu erfahren, das wenig verbreitet ist.

Zum Jeedom-Forum gehen Sie hier: <https://www.jeedom.com/forum/viewtopic.php?f=28&amp;t=24322>


# Begrenztheit

Mi.Licht / EasyBulb / LimitlessLED:
-   Alle Funktionen werden vom Plugin berücksichtigt.

LW12 / Lagute:
-   Das Programmieren von benutzerdefinierten Modi ist nicht möglich. Sie müssen die mit dem Controller gelieferte Anwendung (Magic Home) verwenden. Zum anderen können mit dem Plugin benutzerdefinierte Modi ausgelöst werden.
-   Es gibt verschiedene Versionen von LW12, die möglicherweise nicht mit dem Plugin kompatibel sind.

Magic UFO, MagicHome und Arilux AL-C01 / 02/03/04/06/10:
-   Programmieren im benutzerdefinierten Modus, Radiomodus und Timer werden nicht unterstützt. Sie müssen die mit dem Controller gelieferte Anwendung (Magic Home) verwenden. Zum anderen können mit dem Plugin benutzerdefinierte Modi ausgelöst werden.
-   Es gibt verschiedene Modelle, die möglicherweise nicht mit dem Plugin kompatibel sind.

Xiaomi Yeelight:
-   HSV-Befehle werden nicht unterstützt. Flow und Szene werden erstellt, indem Befehle mit dem JSON-Code erstellt werden, der dem gewünschten Effekt entspricht (siehe YeeLight-API-Dokumentation).
-   Die Xiaomi Nachttischlampe ist nicht kompatibel.
-   Die Xiaomi Mijia Schreibtischlampe ist teilweise kompatibel (keine vollständige Statusrückmeldung).

Wifi 320/370:
-   Der Status der Szenenmodi wird nicht verwaltet, es wird nur EIN / AUS verwaltet.
-   Es gibt verschiedene Modelle, die möglicherweise nicht mit dem Plugin kompatibel sind.

H810:
-   Bühnenspiele werden nicht verwaltet.
-   Es gibt verschiedene Modelle, die möglicherweise nicht mit dem Plugin kompatibel sind.

TP-Link:
-   Timer werden nicht verwaltet.
-   Stromverbrauchsinformationen werden für Glühlampen nicht unterstützt.

Extel Meli:
-   Der Soundteil der Lampe wird nicht berücksichtigt

Xiaomi Philips:
-   Alle Funktionen werden berücksichtigt

Kompatible Glühbirnen oder Stecker Tuya Smart live oder Jinvoo Smart oder eFamilyCloud Apps:
-   Alle Funktionen werden berücksichtigt

Mit Magic Home kompatible Pixel-Pixel-Controller:
-   Benutzerdefinierte Szenen werden nicht unterstützt.

# WLAN-Modulkonfiguration

## Installieren Sie die LEDs

Laden Sie die mobile Anwendung des Herstellers herunter und folgen Sie den Anweisungen, um die LEDs mit dem Mobiltelefon zu steuern. Für jedes wifilightV2-Gerät finden Sie auf der Konfigurationsseite eine ausführliche Hilfe.

Solange die Lampe nicht mit der mobilen Anwendung gesteuert wird, funktioniert das Plugin nicht.

Konsultieren Sie die Hilfe und die Foren des Lampenherstellers.


## Konfigurieren Sie den Router
Sie müssen das DHCP Ihres Routers (in der Regel von Ihrem Diensteanbieter bereitgestellt) konfigurieren, um die Zuweisung der IP-Adresse des WLAN-Moduls oder der Glühlampe oder des Verschlusses so zu ändern, dass sie statisch ist. Bewerten Sie diese Adresse Im Allgemeinen wird es folgende Form haben:
192.168.1.xxx
wobei xxx die Adresse des WLAN-Moduls ist (2 bis 254)

Überprüfen Sie die Foren in Ihrer Box, um zu erfahren, wie Sie Ihr DHCP konfigurieren.

Stellen Sie nach dieser Änderung sicher, dass die Lampen weiterhin von der mobilen App gesteuert werden.

Sie können dann zur Konfiguration des wifilightV2-Plugins wechseln.

## Plugin Installation und Konfiguration

Hilfe:
-   Verwenden Sie das Fragezeichen-Symbol, um Hilfe zu jedem Konfigurationselement zu erhalten.

Einstellung:
-   Um ein Gerät einzurichten, wählen Sie das Menü Plugins / Objekte kommunizieren / wifilightV2
-   Klicken Sie dann oben links auf die Schaltfläche Wifi-Modul hinzufügen
-   Geben Sie den Namen des WLAN-Moduls ein
-   Geben Sie das übergeordnete Objekt ein
-   Wählen Sie die Kategorie Licht (Standard)
-   Aktivieren und sichtbar machen (Standard)
-   Geben Sie die IP-Adresse des Moduls der WLAN-Buchse oder der Glühlampe ein (weitere Informationen finden Sie in den häufig gestellten Fragen).
-   Bei einigen Geräten muss der verwendete Kanal eingegeben werden. Erstellen Sie ein Gerät mit Wifilight V2 pro Kanal
-   Bei einigen Geräten müssen Sie ein Token oder (und) einen Bezeichner eingeben. Weitere Informationen finden Sie in der Hilfe zur Gerätekonfigurationsseite
-   Bei einigen Controllern ist es erforderlich, die Anzahl der LEDs der Pixelstreifen-LEDs anzugeben
-   Bei einigen Controllern muss die Reihenfolge der Farben angegeben werden, wenn die Farben standardmäßig nicht übereinstimmen
-   Geben Sie die Marke oder Kategorie der Lampe, LED-Leiste oder Brücke ein
-   Geben Sie den genauen Typ des Controllers, der Glühlampe, des Steckers oder der LED-Leiste ein. Dies ist wichtig, um die Befehle zur Steuerung des Geräts zu erstellen
-   Geben Sie die Anzahl der Befehlsübertragungen ein: Damit können Sie den Befehl für ein entferntes Gerät bei einer fehlerhaften Übertragung wiederholen. (Standardmäßig 1). Einige Glühbirnen oder Stecker schaffen diese Wiederholung nicht, da das Plugin durch die Rückkehr des Zustands der Übertragung sicherstellt. Einige relative Befehle (Inkremente) werden nicht wiederholt.
-   Geben Sie die Sendeverzögerung bei Wiederholung ein (Standardeinstellung 0 ms, max. 100 ms).
-   Geben Sie die prozentuale Erhöhung der Intensität ein, wenn Sie die Tasten drücken, um die Lichtintensität zu erhöhen oder zu verringern
-   Geben Sie die Gruppennummer für die Synchronisation ein, siehe unten

## Befehle hinzufügen
Beim Speichern des Moduls werden die Befehle automatisch erstellt.

Der Name der Befehle kann geändert werden. Die automatisch erstellten und gelöschten Befehle werden während eines Backups neu erstellt.

Wenn alle Befehle erstellt wurden, können sie die Benutzeroberfläche beschweren. Es ist möglich, sie nicht durch Konfigurieren des Befehls anzuzeigen.

## Änderung der Kategorie oder des Modells der Birne, Fassung oder Leiste

-   Alle Bestellungen entfernen
-   Ändern Sie die Kategorie oder das Modell
-   2 mal sparen

# Funktionsweise der Statusrückmeldung und des Verbindungsstatus

## Kompatibilität der Staatsrückkehr

Die Statusrückmeldung funktioniert mit den Controllern LW12 / Lagute, Magic UFO, Arilux und Wifi 3x0 (teilweise) sowie mit Xiaomi YeeLight-Glühlampen und Kopfband, TP-Link-Glühlampen und Fassungen, Philips Xiaomi-Glühlampen, Glühlampen und Fassungen. Tuya-kompatible Smart-App, Extel Meli und die Magic Home-kompatiblen Pixel-Strip-Controller.

## Prinzip

Die Rückkehr des Status besteht darin, dass Jeedom den Status des Controllers wiederherstellt, wenn sein Status von einem anderen Master als Jeedom geändert wurde: Portable App oder Fernbedienung.

## Jeedoms periodisches Update
LW12 / Lagute, Magisches UFO, Arilux, Wifi 3x0, TP-Link, Extel Meli, Xiaomi Yeelight, Philips Xiaomi, Tuya / Jinvoo / eFamillyCloud-Apps und Pixel-Controller führen kompatible Magie zu Hause ein: jede Minute befragt Jeedom den Controller, die Buchse oder die Glühbirne, um ihren Status zu erfahren und das Tempo der Widgets des Plugins (Schieberegler und Farbe) zu aktualisieren. Informationen, die dem Zustand entsprechen, werden aktualisiert und können von den Szenarien abgefragt werden.

## Update nach Szenario

Die Befehle xxxxGet und Status können in einem Jeedom-Szenario verwendet werden.

## Verbindungsinformationen:

Der Befehl ConnectedGet ruft den Verbindungsstatus jedes Geräts ab. Es wird jede Minute aktualisiert.
-  1: Gerät mit Statusrückmeldung OK
-  2: Gerät kann nicht vorbereitet werden
-  3: Gerät nicht angeschlossen
-  4: keine antwort vom gerät
-  5: falsche Geräteantwort
-  6: Gerät ohne Statusrückmeldung

# Wie die Synchronisation funktioniert

##  Prinzip der Synchronisation

Es ist möglich, mehrere Glühbirnen oder Marken verschiedener Marken zu synchronisieren:

Alle Lampen oder Buchsen, die dieselbe Gruppennummer haben, werden synchronisiert

Gruppe 0 wird nicht synchronisiert (Standardgruppe)

Bei Verwendung einer Steuerung einer Glühlampe oder eines Steckers der Gruppe wird derselbe Befehl auf alle Glühlampen derselben Gruppe angewendet

Wenn der Befehl für die Glühbirne oder die synchronisierte Fassung nicht vorhanden ist, wird er einfach ignoriert.

Achtung, die Lampen werden nicht exakt gleichzeitig bestellt, da sich Latenzen beim Senden von Bestellungen verzögern, die nacheinander ausgeführt werden.

## Synchronisationskonfiguration

Geben Sie einfach eine andere Zahl von Null in das Gruppenfeld ein, wenn Sie das Gerät konfigurieren. Alle Geräte mit den gleichen Nummern werden synchronisiert.

# Sonderfall von Mi.Light Boxen

## IBox 1 oder 2 konfigurieren

Seit Version 1.0.58 von iBox 1 und 2 ist es wichtig, ihre Konfiguration zu ändern, damit sie mit Jeedom kommunizieren können.

Stellen Sie eine Verbindung zu http (mit einem Webbrowser) zur IP-Adresse Ihrer iBox her. Die Standardkennungen sind admin / admin. Gehen Sie zur Registerkarte "Other Setting" und wählen Sie in "Network Parameters setting / Protocol" die Option UDP aus.

# Sonderfall von Xiaomi Yeelight

## Birnenkonfiguration
Die LAN-Steuerung muss unbedingt über die Xiaomi Yeelight-Anwendung aktiviert werden.

## Xiaomi Yeelight-Szenenmodus
Es ist möglich, die Szenenmodi zu konfigurieren. Im Plugin sind verschiedene Szenenmodi vorprogrammiert, es können jedoch weitere Szenenmodi hinzugefügt werden.

Es genügt, bestimmte Bedingungen zu beachten:
-   Fügen Sie einen wifilightV2-Aktionsbefehl vom Typ Default hinzu
-   Vergeben Sie einen Namen (zB Scene Blink)
-   Stellen Sie in Parametern den Szenenbefehl Yeelight ein, zum Beispiel: "id": 1, "method": "set_scene", "params": ["cf", 0,0, "500,1,255,100,1000,1,16776960, 70 „]

Setzen Sie nicht die Start- und Endklammern sowie die Rückkehrzeichen in die Zeile. Das Plugin fügt sie automatisch hinzu
Inspirieren Sie vorkonfigurierte Befehle, um diese zusätzlichen Szenenmodi zu erstellen.

## Update des Lampenstatus in Jeedom
Wenn das Plugin aktiviert wird und sobald der Dämon alle 5 Minuten gestartet wird, sucht das Plugin nach den Glühbirnen, die mit Jeedom verbunden sind.

Sobald die Glühlampe gefunden ist, wird der Zustand der Glühlampe sofort wieder mit dem Plugin verbunden.

Beachten Sie, dass das Plugin bis zu 5 Minuten benötigen kann, um eine Glühbirne zu finden.

# Spezialfall von Philips Xiaomi

## Birnenkonfiguration

Es ist wichtig, ein Token wiederherzustellen, damit das Plugin mit Philips Xiaomi-Geräten interagieren kann.

Das Verfahren ist komplex und erfordert mehrere Manipulationen. Suchen Sie im Internet mit dem folgenden Schlüsselwort: Xiaomi-Token.

Es wird keine Hilfe zum Wiederherstellen des Tokens gegeben.

# Sonderfall kompatibler Glühbirnen und Steckdosen Tuya / Smart live / Jinvoo / eFamillyCloud-Apps

## Glühbirnen- oder Sockelkonfiguration

Es ist wichtig, einen lokalen Schlüssel (LocalKey) und einen Bezeichner wiederherzustellen, über den das Plugin mit diesen Lampen oder Steckern interagieren kann.

Das Verfahren ist komplex und erfordert mehrere Manipulationen. Führen Sie Ihre Websuche mit Schlüsselwörtern durch: Tuya localkey, insbesondere auf Github.

Die Glühbirne oder Steckdose darf nicht mit einer Anwendung auf dem Telefon verbunden sein, da sie sonst nicht auf Jeedoms Befehle reagiert. Es ist daher notwendig, alle Anwendungen zu schließen, die möglicherweise mit der Glühlampe oder dem Stecker verbunden sind.

Wenn die Glühbirne oder der Sockel in der mobilen Anwendung deinstalliert und erneut installiert wird, wird der Schlüssel geändert. Es ist notwendig, den Schlüssel mit dem obigen Verfahren abzurufen.
Es wird keine Hilfe zum Wiederherstellen des Schlüssels oder der Kennung gegeben.

# FAQ


## Welche Lampen oder Stecker können verwendet werden?

Lesen Sie die Dokumentation

## Nichts passiert

Führen Sie zuerst die Lampen mit der vom Hersteller bereitgestellten mobilen Anwendung aus.

Verwenden Sie die Schaltfläche <test> im Menü Plugin / Verbundene Objekte / wifilightV2 /.

Es wird keine Hilfe bereitgestellt, ohne dass die Lampen mit der Lampenanwendung des Herstellers auf einem Mobiltelefon funktionsfähig sind.
Dem Controller oder der Lampe muss eine feste IP-Adresse zugewiesen werden.


## Ich weiß nicht, wie ich meine Internetbox konfigurieren soll

Auf der Box und den Konzepten, die zur Konfiguration des Routers für die Zuweisung einer festen IP-Adresse erforderlich sind, wird keine Hilfe bereitgestellt. Konsultieren Sie die Foren der Box.

## Es werden nicht alle Bestellungen erstellt, wenn Sie eine Glühlampe oder ein Auslassmodell wechseln

Sparen Sie 2 mal.

## Weißlicht-Intensitätsmanagement Mi.Light / EasyBulb / LimitlessLED ist nicht praktisch

Der Hersteller von LEDs hat nicht geplant, die Intensität der Lampe direkt zu beeinflussen. Wir können nur den vorherigen Wert erhöhen oder verringern. Das Plugin repliziert nur diesen Vorgang. Der vorgeschlagene Cursor ist daher launisch.

## Das Farbintensitätsmanagement hat manchmal unerwartetes Verhalten

Kein Protokoll behandelt die Intensität von Farbe, obwohl dies für mobile Apps normalerweise der Fall ist. Solange Jeedom Farbe und Intensität beherrscht, läuft alles gut. Wenn jedoch die Intensität durch eine mobile Anwendung geändert wird, entsprechen die Ergebnisse nicht immer den erwarteten Ergebnissen. Das Plugin versucht das Problem zu beheben, wenn die Lampe oder der Controller eine Rückmeldung haben.

## Gibt es eine Rückkehr des Staates?

Lesen Sie die Dokumentation

## Xiaomi Yeelight-Lampen können nicht betrieben werden

Es ist wichtig, den LAN-Steuermodus über die Xiaomi Yeelight-Anwendung zu aktivieren.

## Ich kann den Klang von Extel Meli-Lampen nicht kontrollieren

Sound wird vom Plugin nicht unterstützt

## Philips Xiaomi-Lampen können nicht verwendet werden

Für die Interaktion mit Philips Xiaomi-Lampen muss ein Token oder Token in Englisch übermittelt werden. Ohne dieses Token wird die Glühlampe die an sie gesendeten Aufträge nicht berücksichtigen. Dieses Token befindet sich in der Mi-Home-App. Je nach Telefon gibt es mehrere Möglichkeiten, das Token wiederherzustellen. Das Verfahren ist an mehreren Stellen beschrieben, wird jedoch aus zwei Hauptgründen hier nicht wiedergegeben:

-   Xiaomi hat sein Protokoll bereits geändert, wodurch das Verfahren zur Wiederherstellung des Tokens geändert werden musste.
-   Neue, einfachere Verfahren können Internetbenutzern zur Verfügung gestellt werden.
-   Diese Dokumentation wird nicht so schnell gepflegt wie eine einfache Suche im Web mit den Schlüsselwörtern: xiaomi token.

## Kompatible Glühbirnen oder Stecker können nicht verwendet werden. Tuya / Smart live / Jinvoo / eFamillyCloud-Apps

Um mit diesen Lampen und Sockeln zu interagieren, müssen Sie einen lokalen Schlüssel oder Localkey oder ein Token in Englisch und einen Bezeichner senden. Ohne diese Parameter berücksichtigt die Glühlampe nicht die an sie gesendeten Aufträge. Es gibt mehrere Methoden, um diese Informationen abzurufen. Das Verfahren ist an mehreren Stellen beschrieben, wird jedoch aus zwei Hauptgründen hier nicht wiedergegeben:

-   Die Anwendungen wurden aktualisiert, was bedeutet, dass das Verfahren zum Abrufen der Informationen geändert wurde.
-   Neue, einfachere Verfahren können Internetbenutzern zur Verfügung gestellt werden.
-   Diese Dokumentation wird nicht so schnell gepflegt wie eine einfache Suche im Web mit den Schlüsselwörtern: Tuya LocalKey und insbesondere auf Github.

# Wie bekomme ich Hilfe?
Zum Jeedom-Forum gehen Sie hier: <https://forum.jeedom.fr/viewtopic.php?f=28&amp;t=2840>

# Schwierigkeiten

Fehler beim Senden des Befehls / des gekerbten Rads ohne Stopp / Ausgabe ohne Stopp

-   wifilightV2-Geräte müssen aktualisiert werden
-   Gehen Sie in jedes Gerät und sparen Sie 2-mal
-   Testen Sie mit neuen Geräten, wenn dies fortbesteht

Mi.Light Bridge IBox1, iBox2, V6: Befehl wird zufällig berücksichtigt
-   Bestellungen werden zu schnell versendet
-   Setzen Sie in Szenarien Pausen von ausreichender Dauer ein

Mi.Light Bridge IBox1, iBox2, V6: Befehl nicht berücksichtigt
-   Wählen Sie beim Koppeln mit der mobilen Mi.Light-Anwendung und beim Konfigurieren von wifilightV2 dieselbe Fernbedienung.

LW12 UFO Magic Arilux Wifi3x0 H801 kompatibles Magic Home-System Tuya / Jinvoo / eFamillyCloud-Controller
-   Viele vorhandene Versionen und einige sind möglicherweise mit dem Plugin nicht kompatibel. Kontaktieren Sie den Autor für ein mögliches Update.

Aufträge fehlen beim Erstellen oder Ändern des Untertyps der Glühlampe
-   Speichern Sie die Ausrüstung (2 Mal)



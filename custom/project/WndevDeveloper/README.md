Developer
========

Eine Sammlung von Tools für Entwicklung & Deployments. U.a. kann zwischen verschiedenen Instanzen der Zustand der Plugins synchron gehalten werden.

Benutzung
---------

Das Plugin beinhaltet folgende Funktionssets:

* LESS-Variablen: Kopiert LESS-Variablen zwischen Datei, Datenbank und PHP-Klasse hin und her.
* Pluginlist: Synchronisiert über Instanzen hinweg den Zustand von Plugins.

### Pluginlist

Mit den `wndev:pluginlist`-Konsolenkommandos kann der Zustand der Plugins im Repo hinterlegt werden. Damit können alle anderen Instanzen dieses Repos den Zustand der Plugins wieder bei sich einspielen.

Dazu wird eine Datei namens `plugins.json` im Wurzelverzeichnis des Projekts angelegt. In dieser Datei wird vermerkt, ob ein Plugin _aktiv_ ist, der _Zeitpunkt der letzten Installation_, und der _Zeitpunkt der letzten Aktualisierung_. Darüber hinaus wird die Versionsnummer des Plugins gespeichert.

Mit den folgenden Kommandos kann der Zustand zwischen Datei und Instanz abgeglichen werden:

```shell
php bin/console wndev:pluginlist:status  # Zeigt den Unterschied zwischen plugins.json und DB an
php bin/console wndev:pluginlist:update  # Kopiert Zustand von DB nach plugins.json
php bin/console wndev:pluginlist:install # Kopiert Zustand von plugins.json nach DB
```

Als Workflow empfiehlt es sich also, bei Änderungen in Plugins, Neuinstallation oder Deaktivierung von Plugins `wndev:pluginlist:update` auszuführen und die geänderte `plugins.json` ins Repo einzuspielen.

Wenn nach einem `git pull` die `plugins.json` geändert wurde, sollte wiederum ein `wndev:pluginlist:install` durchgeführt werden, um den Zustand in der eigenen Instanz dem Zustand im Repo anzugleichen.

Beispiel:

```
root@develop:/var/www/# php src/bin/console wndev:pluginlist:status

+---------------------------+-----------------------+---------------+------------------+
| Plugin                    | Needs (re)installing? | Needs update? | Needs disabling? |
+---------------------------+-----------------------+---------------+------------------+
| AdvancedMenu              | No                    | No            | No               |
| Cron                      | No                    | No            | No               |
| WndevDeveloper            | Yes                   | No            | No               |
| WndevForms                | No                    | No            | No               |
| WndevLocationWidget       | No                    | No            | No               |
| WndevSocialShare          | No                    | No            | No               |
| WNNoShop                  | No                    | No            | No               |
| WNVariants                | No                    | No            | No               |
+---------------------------+-----------------------+---------------+------------------+
1 plugins need (re)installing
0 plugins need updating
0 plugins need disabling

root@develop:/var/www/# php src/bin/console wndev:pluginlist:install
1 plugin(s) (re)installed

```

Autor
-----

[web-netz GmbH](https://www.web-netz.de/) 2019

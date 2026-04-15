# Self-Elevation Feature

Diese Erweiterung fügt Self-Elevation Funktionalität zur UniFi API Browser Anwendung hinzu.

## Überblick

Die Self-Elevation ermöglicht es autorisierten Benutzern, sich temporär zu Administrator-Rechten zu erheben, indem sie ihr Passwort erneut eingeben.

## Benutzerrollen

Es gibt drei verschiedene Benutzerrollen:

### 1. **admin**
- Permanente Administrator-Rechte
- Keine Notwendigkeit zur Elevation
- Voller Zugriff auf alle Funktionen

### 2. **elevatable**
- Kann sich temporär zum Admin erheben
- Muss Passwort erneut eingeben zur Bestätigung
- Sieht ein Shield-Icon in der Navbar
- Kann sich jederzeit wieder de-elevieren

### 3. **user**
- Standard-Benutzer ohne Elevation-Rechte
- Kein Zugriff auf Admin-Funktionen
- Kein Shield-Icon sichtbar

## Konfiguration

### Schritt 1: Benutzerdatei erstellen

Erstellen Sie `config/users.php` basierend auf `config/users-template.php`:

```php
<?php
$users = [
    [
        'user_name' => 'alice',
        'password'  => 'SHA512_HASH_HIER',
        'role'      => 'elevatable',
    ],
    [
        'user_name' => 'bob',
        'password'  => 'SHA512_HASH_HIER',
        'role'      => 'admin',
    ],
];
```

### Schritt 2: Passwort-Hash generieren

Nutzen Sie einen SHA512 Hash Generator wie:
- https://passwordsgenerator.net/sha512-hash-generator/

### Schritt 3: Rolle zuweisen

Setzen Sie das `role` Feld auf einen der folgenden Werte:
- `'user'` - Standard-Benutzer
- `'elevatable'` - Kann sich selbst erheben
- `'admin'` - Permanenter Admin

## Verwendung

### Als Benutzer mit elevatable Rolle:

1. Nach dem Login erscheint ein **Shield-Icon** (🛡️) in der oberen rechten Navbar
2. Klicken Sie auf das Shield-Icon
3. Ein Modal öffnet sich und fordert Sie zur Passwort-Eingabe auf
4. Geben Sie Ihr Passwort ein und klicken Sie auf "Elevate"
5. Bei erfolgreicher Authentifizierung verwandelt sich das Shield in eine **Krone** (👑)
6. Sie haben nun Admin-Rechte
7. Klicken Sie erneut auf die Krone, um sich zu de-elevieren

## Implementierte Dateien

- **elevate.php** - Backend-Handler für Elevation-Requests
- **templates/components/elevation_modal.html.twig** - Modal für Passwort-Eingabe
- **templates/layout/main.html.twig** - Erweitert mit Elevation-Button
- **js/custom.js** - JavaScript-Logik für Elevation
- **login.php** - Erweitert um Rollen-Speicherung in Session
- **config/users-template.php** - Template mit Rollen-Feld

## Sicherheitshinweise

1. **Passwort-Verifizierung**: Bei jeder Elevation wird das Passwort erneut überprüft
2. **Session-basiert**: Der Elevation-Status wird in der PHP Session gespeichert
3. **Logging**: Alle Elevation- und De-Elevation-Vorgänge werden geloggt
4. **HTTPS empfohlen**: Verwenden Sie immer HTTPS, um Passwörter zu schützen

## API Endpoints

### POST /elevate.php

**Elevation durchführen:**
```json
{
  "action": "elevate",
  "password": "user_password"
}
```

**De-Elevation durchführen:**
```json
{
  "action": "de-elevate"
}
```

**Status abfragen:**
```json
{
  "action": "check"
}
```

## Session-Variablen

- `$_SESSION['user_role']` - Die Rolle des Benutzers ('user', 'elevatable', 'admin')
- `$_SESSION['is_elevated']` - Boolean, ob der Benutzer derzeit elevated ist
- `$_SESSION['elevation_time']` - Timestamp der letzten Elevation

## Troubleshooting

**Problem**: Shield-Icon erscheint nicht
- Überprüfen Sie, ob `$_SESSION['user_role']` auf 'elevatable' oder 'admin' gesetzt ist
- Stellen Sie sicher, dass Sie eingeloggt sind

**Problem**: Elevation schlägt fehl
- Überprüfen Sie das eingegebene Passwort
- Schauen Sie in die PHP Error Logs
- Stellen Sie sicher, dass `config/users.php` existiert und lesbar ist

**Problem**: Icon ändert sich nicht
- Öffnen Sie die Browser-Konsole und suchen Sie nach JavaScript-Fehlern
- Leeren Sie den Browser-Cache
- Überprüfen Sie, ob `js/custom.js` korrekt geladen wird

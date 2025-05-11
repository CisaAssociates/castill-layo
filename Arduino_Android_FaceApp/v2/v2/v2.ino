#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// Define pins
#define RST_PIN 4
#define SS_PIN 5
#define BUZZER_PIN 2  // <-- Attach buzzer to GPIO 2

const char* ssid = "Jasper22";
const char* password = "jasper22";
const char* serverName = "http://192.168.20.96:8000/api/rfid"; // Laravel API endpoint

MFRC522 mfrc522(SS_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2); // Address 0x27, 16 columns, 2 rows

void setup() {
  Serial.begin(115200);
  SPI.begin();
  mfrc522.PCD_Init();

  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Connecting WiFi");

  pinMode(BUZZER_PIN, OUTPUT); // <-- Set buzzer pin as output

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.println("Connecting to WiFi...");
  }
  
  Serial.println("Connected to WiFi");
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("WiFi Connected");
  delay(1000);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Scan RFID Card");
}

void loop() {
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }

  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }

  String rfid_uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    rfid_uid += String(mfrc522.uid.uidByte[i], HEX);
  }
  rfid_uid.toUpperCase();

  Serial.println("RFID UID: " + rfid_uid);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("UID:");
  lcd.setCursor(0, 1);
  lcd.print(rfid_uid);

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{\"rfid\":\"" + rfid_uid + "\"}";

    int httpResponseCode = http.POST(jsonPayload);
    
    Serial.println("Response code: " + String(httpResponseCode));
    String response = http.getString();
    Serial.println(response);

    lcd.clear();
    if (httpResponseCode == 200) {
      lcd.setCursor(0, 0);
      lcd.print("Success");

      // Success beep: Short beep
      digitalWrite(BUZZER_PIN, HIGH);
      delay(100);
      digitalWrite(BUZZER_PIN, LOW);

    } else {
      lcd.setCursor(0, 0);
      lcd.print("Failed");

      // Failure beep: 2 short beeps
      for (int i = 0; i < 2; i++) {
        digitalWrite(BUZZER_PIN, HIGH);
        delay(100);
        digitalWrite(BUZZER_PIN, LOW);
        delay(100);
      }
    }

    http.end();
  } else {
    Serial.println("WiFi Disconnected");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("WiFi Lost");

    // WiFi lost beep: Long beep
    digitalWrite(BUZZER_PIN, HIGH);
    delay(500);
    digitalWrite(BUZZER_PIN, LOW);
  }

  delay(3000);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Scan RFID Card");
}

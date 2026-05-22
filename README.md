<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Описание

API-сервис для интеграции с VK, Telegram и WHOIS. Принимает вебхуки, отправляет сообщения, сохраняет входящие данные и отдаёт их потребителям.

---

## Структура БД

### `users`
Стандартная таблица Laravel для аутентификации.

| Колонка | Тип | Примечание |
|---|---|---|
| id | bigint | PK |
| name | varchar | |
| email | varchar | unique |
| email_verified_at | timestamp | nullable |
| password | varchar | |
| remember_token | varchar(100) | nullable |

### `telegram_in_msg`
Входящие сообщения от Telegram (вебхуки).

| Колонка | Тип | Примечание |
|---|---|---|
| id | bigint | PK |
| telegram_user_id | bigint | nullable, index |
| telegram_message_id | bigint | nullable, index |
| username | varchar | nullable |
| first_name | varchar | nullable |
| last_name | varchar | nullable |
| language_code | varchar(12) | nullable |
| text | text | nullable |
| command | varchar(64) | nullable, index |
| is_start | boolean | default false, index |
| bot_token_hash | varchar(64) | nullable, index — sha256 токена бота |
| payload | json | полный входящий payload |
| received_at | timestamp | nullable, index |
| created_at/updated_at | timestamp | |

### `vk_group`
Группы VK для отправки сообщений.

| Колонка | Тип | Примечание |
|---|---|---|
| id | bigint | PK |
| group_name | varchar(64) | unique |
| token | varchar(512) | токен VK API |
| comment | varchar | nullable |
| payed | boolean | default false |
| payed_date | date | nullable |
| created_at/updated_at | timestamp | |

### `vk_incoming_messages`
Входящие callback-запросы от VK API с отметкой о доставке.

| Колонка | Тип | Примечание |
|---|---|---|
| id | bigint | PK |
| channel | varchar | nullable, index — идентификатор канала |
| payload | json | полный входящий callback от VK |
| is_delivered | boolean | default false, index — отдан потребителю или нет |
| delivered_at | timestamp | nullable — когда отдан |
| received_at | timestamp | nullable, index |
| created_at/updated_at | timestamp | |

---

## API Endpoints

### Health

```
GET /api/health
```
Проверка работоспособности. Ответ: `{"status": "ok"}`.

---

### WHOIS

```
GET /api/whois?domain=example.com
```
WHOIS-проверка домена.

**Параметры:**
- `domain` (string, required) — домен для проверки

**Ответ:**
```json
{
  "status": 1,
  "domain": "example.com",
  "available": false,
  "info": { ... },
  "message": null
}
```

---

### Telegram

```
ANY /api/telegram
```
Двойного назначения: приём вебхуков от Telegram и отправка исходящих сообщений.

**Входящий вебхук:** если в теле JSON есть `message` — сохраняет в `telegram_in_msg` и обрабатывает команды (`/start`, `/get_my_id`, `/link-to-alfa`).

**Исходящее сообщение:** параметры `msg` + `s` (подпись) + `id` (получатели).

Подпись `s` = `md5(domain)` — отправка всем `id[]` + администратору.  
Подпись `s` = `md5('1')` — отправка только администратору.

Параметр `answer=json` форсирует JSON-ответ, иначе возвращается plain text.

---

```
ANY /api/telegram/webhook
```
Выделенный приёмник вебхуков Telegram. Читает raw JSON, сохраняет в `telegram_in_msg`.  
Ответ: `{"res": true}`.

---

### VK — Отправка сообщений

```
ANY /api/vk/send
```
Отправка сообщения пользователям VK от имени группы.

**Параметры:**
- `group_name` (string, опционально) — название группы (токен берётся из `vk_group`)
- `token` (string, опционально) — прямой токен VK API
- `user_id` (required) — число, строка "1,2,3" или массив
- `message` (string, required) — текст сообщения

**Ответ:**
```json
{
  "ok": true,
  "group": "main_group",
  "sent_user_ids": [12345]
}
```

---

### VK — Приём callback (вебхук)

```
POST /api/vk/webhook?channel=my_channel
```
Принимает callback от VK Callback API.  
Если `type: "confirmation"` — отвечает confirmation code (plain text).  
Остальные события сохраняет в `vk_incoming_messages` с `is_delivered = false`.

**Параметры:**
- `channel` (query, опционально) — привязывает запись к каналу

**Тело запроса:**
```json
{
  "type": "message_new",
  "group_id": 123456,
  "object": { ... },
  "secret": "..."
}
```

**Ответ:** `{"ok": true}`

---

### VK — Получение неотданных запросов

```
GET /api/vk/incoming?channel=my_channel
```
Возвращает все неотданные записи из `vk_incoming_messages` (сортировка по id ASC) и сразу помечает их как доставленные (`is_delivered = true`, `delivered_at = now()`).

**Параметры:**
- `channel` (query, опционально) — фильтр по каналу

**Ответ:**
```json
[
  {
    "id": 1,
    "channel": "my_channel",
    "payload": { ... },
    "received_at": "2026-05-22T12:00:00+0000"
  }
]
```

---

### VK — Лог входящих сообщений (веб-интерфейс)

```
GET /vk/incoming/log
```
Страница для просмотра записей `vk_incoming_messages` в браузере.  
Фильтры: по каналу, по статусу доставки. Пагинация. Кнопка автообновления каждые 15 секунд.

---

## Telegram Log (веб-интерфейс)

```
GET /telegram/log
```
Просмотр входящих сообщений Telegram (`telegram_in_msg`). Поиск, фильтры по типу и периоду, пагинация.

---

## Laravel Log (веб-интерфейс)

```
GET /laravel/log
```
Просмотр логов Laravel.

---

## Схема работы VK Incoming Messages

```
VK Callback API  ──POST──►  /api/vk/webhook  ──►  vk_incoming_messages (is_delivered = false)
                                                          │
                                                  GET /api/vk/incoming
                                                          │
                                                          ▼
                                          возврат записей + is_delivered = true
```

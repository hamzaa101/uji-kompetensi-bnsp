# API / Polling Documentation

## GET /notifications/latest

- Auth: login.
- Response:

```json
[{"id":1,"title":"Stok kritis","message":"Cefixime tersisa sedikit","type":"warning","is_read":false}]
```

## GET /notifications/unread-count

- Auth: login.
- Response:

```json
{"count":3}
```

## POST /notifications/{id}/read

- Auth: login.
- CSRF: required.
- Response:

```json
{"ok":true}
```

## GET /admin/monitoring/latest

- Auth: admin.
- Response:

```json
{"memory_usage":29360128,"disk_usage":125829120,"queue_pending":0,"request_count":1,"error_count":3,"avg_response_time":0}
```

## GET /admin/orders/latest-count

- Auth: admin.
- Response:

```json
{"count":5}
```

## Error Example

```json
{"message":"This action is unauthorized."}
```

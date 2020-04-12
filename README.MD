# IMGHUB
A simple image hub.

## Install
`composer require thatfixer/imghub`

## Usage
`$url = \Imghub\Imghub::hub('HubName')->upload('/path/to/the/file')->url();`

## Hubs
| HubName | SizeLimit | MimeLimit |
|---|---|---|
| baidu | 10MB | image/jpeg,image/png,image/gif,image/pjpeg,image/x-png,image/webp,application/octet-stream |
| netease | 3MB | image/jpeg,image/png,image/gif |
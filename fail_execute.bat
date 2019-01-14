@echo off
set "batchisin=%~dp0"
cd %batchisin%
cd src\php7
start http:\\localhost:8080
php.exe -S localhost:8080 -t %batchisin%/src
pause 
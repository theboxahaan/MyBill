@echo off
:: BatchGotAdmin
::-------------------------------------
REM  --> Check for permissions
>nul 2>&1 "%SYSTEMROOT%\system32\cacls.exe" "%SYSTEMROOT%\system32\config\system"

REM --> If error flag set, we do not have admin.
if '%errorlevel%' NEQ '0' (
    echo Requesting administrative privileges...
    goto UACPrompt
) else ( goto gotAdmin )

:UACPrompt
    echo Set UAC = CreateObject^("Shell.Application"^) > "%temp%\getadmin.vbs"
    set params = %*:"="
    echo UAC.ShellExecute "cmd.exe", "/c %~s0 %params%", "", "runas", 1 >> "%temp%\getadmin.vbs"

    "%temp%\getadmin.vbs"
    del "%temp%\getadmin.vbs"
    exit /B

:gotAdmin
    pushd "%CD%"
    CD /D "%~dp0"
::--------------------------------------

::ENTER YOUR CODE BELOW:
cls
echo INSTALLING PRINTER MORTY...
cscript "%SYSTEMDRIVE%\Windows\System32\Printing_Admin_Scripts\en-US\prnmngr.vbs" -a -p "Morty" -m "Generic / Text Only" -r "usb001"
cscript "%SYSTEMDRIVE%\Windows\System32\Printing_Admin_Scripts\en-US\prncnfg.vbs" -t -p "Morty" -h "Morty" +shared     
pause
@echo off
rem Copy files in our folder structure to development installation.
setlocal enabledelayedexpansion
pushd acumulus.ocmod
pushd upload
for %%D in (admin catalog) do (
pushd %%D
for /R %%F in (*) do (
  set B=%%F
  del D:\Projecten\Acumulus\OpenCart\www23\www\!B:%CD%\=!
  mklink /H D:\Projecten\Acumulus\OpenCart\www23\www\!B:%CD%\=! D:\Projecten\Acumulus\Webkoppelingen\OpenCart23\acumulus.ocmod\upload\!B:%CD%\=!
)
popd
)
popd
popd
setlocal disabledelayedexpansion

mklink /J D:\Projecten\Acumulus\OpenCart\www23\www\system\library\Siel D:\Projecten\Acumulus\Webkoppelingen\OpenCart23\acumulus.ocmod\upload\system\library\Siel

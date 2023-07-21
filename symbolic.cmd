::wmic logicaldisk get caption
@echo off
for /f "skip=1 delims=:" %%x in ('wmic logicaldisk get caption') do call :Foo %%x
goto End

:Foo

if [%1]==[] goto Continue
		
set link_drive=%1
::echo %link_drive%
::echo %link_drive%:
mklink /d %cd%\%link_drive% %link_drive%:\

goto :eof

:End

:Continue
rem

::mklink /d %cd%\d D:\
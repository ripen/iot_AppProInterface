; SEE THE DOCUMENTATION FOR DETAILS ON CREATING INNO SETUP SCRIPT FILES!

[Setup]
; NOTE: The value of AppId uniquely identifies this application.
; Do not use the same AppId value in installers for other applications.
; (To generate a new GUID, click Tools | Generate GUID inside the IDE.)
AppId={{AA598298-EBD8-4796-8E94-62719B3BEC23}
AppName=AnyChat for Web Plugin
AppVersion=1.0
AppPublisher=广州佰锐网络科技有限公司
AppPublisherURL=http://www.bairuitech.com/
AppSupportURL=http://www.bairuitech.com/
AppUpdatesURL=http://www.bairuitech.com/
DefaultDirName={pf}\BaiRuiTech\AnyChatWeb
DefaultGroupName=AnyChatWeb
AllowNoIcons=yes
OutputDir=./
OutputBaseFilename=AnyChatWebSetup_SelfBuild
Compression=lzma
SolidCompression=yes

[Languages]
Name: "chinese"; MessagesFile: "compiler:Languages\Chinese.isl"

[Files]
Source: "AnyChatWeb\npanychatweb.dll"; DestDir: "{app}\"; Flags: ignoreversion restartreplace regserver
Source: "AnyChatWeb\npvideoshowctrl.dll"; DestDir: "{app}\"; Flags: ignoreversion restartreplace regserver
Source: "AnyChatWeb\*"; DestDir: "{app}\"; Flags: ignoreversion recursesubdirs createallsubdirs

; NOTE: Don't use "Flags: ignoreversion" on any shared system files

[registry]
; register npanychatweb.dll for firefox plugin
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/anychat;ValueType: string; ValueName:Path; ValueData:{app}\npanychatweb.dll;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/anychat;ValueType: string; ValueName:Description; ValueData:AnyChat® for Web SDK Plugin;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/anychat;ValueType: string; ValueName:ProductName; ValueData:AnyChat® Platform Core SDK;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/anychat;ValueType: string; ValueName:Vendor; ValueData:GuangZhou BaiRui Network Technology Co., Ltd.;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/anychat;ValueType: string; ValueName:Version; ValueData:1.0.0.1 ;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/anychat\MimeTypes\application/anychat-plugin;Flags: createvalueifdoesntexist

; register npvideoshowctrl.dll for firefox plugin
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/videoshow;ValueType: string; ValueName:Path; ValueData:{app}\npvideoshowctrl.dll;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/videoshow;ValueType: string; ValueName:Description; ValueData:AnyChat® for VideoShow Plugin;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/videoshow;ValueType: string; ValueName:ProductName; ValueData:AnyChat® Platform Core SDK;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/videoshow;ValueType: string; ValueName:Vendor; ValueData:GuangZhou BaiRui Network Technology Co., Ltd.;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/videoshow;ValueType: string; ValueName:Version; ValueData:1.0.0.1 ;Flags: uninsdeletevalue
Root:HKLM;Subkey:SOFTWARE\MozillaPlugins\@bairuitech.com/videoshow\MimeTypes\application/anychat-video;Flags: createvalueifdoesntexist

; register npanychatweb.dll for IE
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{91CC58C4-BA8A-400D-A176-856EDF42CB57}\iexplore;ValueType: dword; ValueName:Blocked; ValueData:1;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{91CC58C4-BA8A-400D-A176-856EDF42CB57}\iexplore;ValueType: dword; ValueName:Count; ValueData:2;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{91CC58C4-BA8A-400D-A176-856EDF42CB57}\iexplore;ValueType: dword; ValueName:Flags; ValueData:4;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{91CC58C4-BA8A-400D-A176-856EDF42CB57}\iexplore;ValueType: dword; ValueName:Type; ValueData:1;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{91CC58C4-BA8A-400D-A176-856EDF42CB57}\iexplore\AllowedDomains\*;ValueType: string; ValueName:(默认); ValueData:;Flags: uninsdeletevalue

; register npvideoshowctrl.dll for IE
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{B685A393-905F-45B5-B26E-FF199EEE2FD7}\iexplore;ValueType: dword; ValueName:Blocked; ValueData:1;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{B685A393-905F-45B5-B26E-FF199EEE2FD7}\iexplore;ValueType: dword; ValueName:Count; ValueData:2;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{B685A393-905F-45B5-B26E-FF199EEE2FD7}\iexplore;ValueType: dword; ValueName:Flags; ValueData:4;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{B685A393-905F-45B5-B26E-FF199EEE2FD7}\iexplore;ValueType: dword; ValueName:Type; ValueData:1;Flags: uninsdeletevalue
Root:HKCU;Subkey:Software\Microsoft\Windows\CurrentVersion\Ext\Stats\{{B685A393-905F-45B5-B26E-FF199EEE2FD7}\iexplore\AllowedDomains\*;ValueType: string; ValueName:(默认); ValueData:;Flags: uninsdeletevalue










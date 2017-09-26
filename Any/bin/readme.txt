1、AnyChatWebSetup.exe	已经打包、签名的插件安装程序

2、AnyChatWeb	没有打包、签名的插件

3、InnoSetup5	插件打包工具（安装程序制作工具）

4、AnyChatWeb.iss	插件打包脚本

5、selfbuild.bat	自动打包批处理，运行后，会自动将“AnyChatWeb”目录中的文件打包，生成新的插件安装程序

	如果您自己有ActiveX代码签名证书，可对“AnyChatWeb”目录中的“npanychatweb.dll”和“npvideoshowctrl”插件进行签名，然后运行“selfbuild.bat”批处理生成安装程序，然后再对安装程序进行签名，则插件安装、运行过程中将出现您签名的公司名称。
	
	您可以修改插件打包脚本（可用记事本编辑AnyChatWeb.iss文件），修改其中的产品名称、公司信息等，则打包后生成的插件安装程序将是您的专属安装包。
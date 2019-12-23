Import-Module WebAdministration
For($i=0;$i -lt $args.Count; $i++)
{
    if(!(Test-Path D:\wordpress\$($args[$i]))){
        cp -r D:\wordpress\wordpress D:\wordpress\$($args[$i])
    }
    #Write-Host $args
    #exit
    #�½�Ӧ�ó���� api.dd.com
    #New-Item iis:\AppPools\api.dd.com
    #Set-ItemProperty iis:\AppPools\$1 managedRuntimeVersion v4.0 #����Ӧ�ó���ذ汾Ϊ4.0��Ĭ��Ϊ2.0��Windows Server 2008 R2��
    #�½�վ�� api.dd.com������ͷΪ api.dd.com��·��Ϊ d:\apidd
    New-Item iis:\Sites\$($args[$i]) -bindings @{protocol="http";bindingInformation=":80:$($args[$i])"} -physicalPath D:\wordpress\$($args[$i])
    #Ϊվ�� api.dd.com �������ͷ imageapi.dd2.com
    #New-WebBinding -Name "$args" -IPAddress "*" -Port 80 -HostHeader $args
    #Ϊվ�� api.dd.com ����Ӧ�ó����Ϊ api.dd.com
    Set-ItemProperty IIS:\Sites\$($args[$i]) -name applicationPool -value DefaultAppPool
    #��վ��api.dd.com���½�Ӧ�ó���cust_account_api ��Ŀ¼ΪD:\cust_account_api_new
    #new-item iis:\sites\$args\cust_account_api -type Application -physicalpath D:\wordpress\$args\cust_account_api_new
    #Set-ItemProperty IIS:\Sites\$args\cust_account_api -name applicationPool -value $args
    #��վ��ServerLog���½�����Ŀ¼cust_account_api ��Ŀ¼ΪD:\cust_account_api_new\log
    #new-item D:\wordpress\$args\cust_account_api_new\log -type directory -force

}

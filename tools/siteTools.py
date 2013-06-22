import urllib2
import urllib
import re

debug=False

def getCiteInfo(title):
    url = "http://scholar.google.com/scholar"

    #title = "Efficient approach based on hybrid bounding volume hierarchy for real-time collision detection"
    param = {}
    param['hl'] = 'en'
    param['q'] = title

    url = url + '?' + urllib.urlencode(param)
    if(debug):
        print url
    headers = {'User-Agent' : 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1541.0'}

    req = urllib2.Request(url, '', headers)
    response = urllib2.urlopen(req)
    result = response.read()
    #the first <h3 is the right anwser for the search
    #first div class="gs_r"
    firstindex_begin = result.find('<div class=\"gs_r\" style=')
    firstindex_end = result.find('<div class=\"gs_r\" style=', firstindex_begin+1)
    paperinfo = result[firstindex_begin : firstindex_end]
    #print paperinfo
    citeid_begin = paperinfo.find('gs_ocit(event,') 
    citeid_end = paperinfo.find(')', citeid_begin)
    citeid = paperinfo[citeid_begin + len('gs_ocit(event,') + 1 : citeid_end-1]
    if(debug):
        print citeid

    if(len(citeid) == 0):
        print title + "cannot find."
        return title + "cannot find"
    response.close()
    citeurl = "http://scholar.google.com/scholar.bib?"
    params={}
    params['q']='info:'+citeid+':scholar.google.com/'
    params['output']='citation'
    citeurl += urllib.urlencode(params)
    if(debug):
        print citeurl
    headers['Referer'] = url
    headers['Host'] = 'scholar.google.com'
    headers['Cookie'] = 'PREF=ID=84309ecaa38d4f1d:U=8efa2cc2f9e0429b:LD=en:NW=1:TM=1355384776:LM=1358864435:S=JijFkDvpIAs5wAon; Hm_lvt_3d143f0a07b6487f65609d8411e5464f=1371785664,1371785824,1371818985,1371819228; HSID=ASgsSlq32YNaxir2S; APISID=XitTo75V0cr23epE/AF4Ge090GczlKFDnU; NID=66=HwuitIYtSv3YyN4L6Y3LWGGQkyuqwCsHpkMBXal1EGj8RuDkdHHUT_-HASz5l5Zxlf3tvLbqfzl0HPoZ8363L9n7w18EfXEb5yVX7oKreNV6JxdMFBdHiIEfEZuoabZAZ9_yXEyaDbVFPwM0Hqu0sorEww9JaqIJC_SSpfjtg9Hg7IvpZpyxXZjHdKUcY3SxMPi1KA; GSP=ID=84309ecaa38d4f1d:CF=4:S=T69HMRmqUcD1wyFO; SID=DQAAAMIAAAC4daJp0ie4VOIvSomr7GybUbJ8xoBefHr1DLCyoyjtcKd1gJo31uOgWGedH-J3J6dePm9vn2nP5lo-gp2QgbNFLYtkzdVZBqZX2zcjCniTmtktjNP4-C6rpus-lP6V2V13ryWMXbik6sPw3b1Ohx6AnVsfRm5zbHNwJNlaGr8SNfg5AQRWUjVa7MwzvDgmgPLLIvx3jNIJVSdZ4_K0fZntW4h_7RXiRXXPbfFCKV7gv9Ox_nz8AYodtuJmAsKYEj3sKlbcmfMBfWFuy8d9__H'
    req = urllib2.Request(citeurl, '', headers)

    try:
        response = urllib2.urlopen(req)
    except urllib2.HTTPError:
        print 'error, request again'
        response = urllib2.urlopen(req)

    result = response.read()
    return result

import sys
if __name__ == '__main__':
    argvlen = len(sys.argv)
    if(argvlen == 1):
       exit() 
    print getCiteInfo(sys.argv[1])


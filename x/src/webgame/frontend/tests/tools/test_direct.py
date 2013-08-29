#!/usr/bin/env python
#-*- coding: utf-8 -*-

import urllib2, urllib
import hmac, hashlib

handler = urllib2.HTTPHandler(debuglevel=1)
opener = urllib2.build_opener(handler)
urllib2.install_opener(opener)

url = "http://api.kaqu.cc:8082/pay/direct"
data = {
    "q0_MerId" : "10000001",
    "q1_AppId" : "123",
    "q2_uid" : "ioqwrelkjlkdgkjeroitdlkgds",
    "q3_Amount" : "50",
    "q4_Url" : "http://callback.test1.cc/",
    "qa_Memo" : "this is message",
    "qk_cardTp" : "UNICOM",
    "qk_cardNo" : "123889435694567974569",
    "qk_cardPwd" : "9387453759357939543495",
    "qk_cardAmt" : "50",
    # "hmac" : "6fa76b1c5798816bbd56f6518c4d6df67ab5e7ace8a2c3c6732bcbbc6fe353fd",
}
merchantKey = "1234567812345678"
keys = ["q0_MerId", "q1_AppId", "q2_uid", "q3_Amount", "q4_Url", "qa_Memo", "qk_cardTp", "qk_cardNo", "qk_cardPwd", "qk_cardAmt"]
data["hmac"] = hmac.new(merchantKey, "".join([data[k] for k in keys]), hashlib.sha256).hexdigest()

try:
    print urllib2.urlopen(url, urllib.urlencode(data)).read()
except urllib2.HTTPError as err:
    from html2text import html2text
    print html2text(err.read().decode("utf-8"))

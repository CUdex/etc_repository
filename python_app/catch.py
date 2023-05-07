# create by cuyu9779
import re
import requests
from bs4 import BeautifulSoup
from datetime import date, timedelta
import boto3

message = """새로운 채용 공고 확인
"""
newline = "\n"
urls_title = [["https://agro.seoul.go.kr/archives/category/institution_c1/institution_news_c1/news_employ-n1", "[서울농업기술센터]"], ["https://blog.naver.com/PostList.naver?blogId=ssong-ajumma&from=postList&categoryNo=22", "[공공일자리도우미]"]]
header = {'User-Agent':'Mozilla/5.0'}
html_selector = {"[서울농업기술센터]":["div.child_policyDL_R", "h3 > a", "span", "%Y-%m-%d"], "[공공일자리도우미]": ["div.se-section.se-section-documentTitle.se-l-default.se-section-align-left", "span.se-fs-.se-ff-", "span.se_publishDate.pcol2", "%Y. %-m. %-d."]}

yesterday = date.today() - timedelta(1)

sub_count = 0

for url in urls_title:
    message += url[1] + newline
    
    yesterday_str = yesterday.strftime(html_selector[url[1]][3])
    pattern = re.compile(f'^{yesterday_str}')
    response = requests.get(url[0], headers=header)
    soup = BeautifulSoup(response.text, "html.parser")
    origin = soup.select(html_selector[url[1]][0])

    print(yesterday_str)

    for body in origin:
        day = body.select_one(html_selector[url[1]][2])
        day = day.get_text()
    
        if re.match(pattern, day):
            title = body.select_one(html_selector[url[1]][1])
            title = title.get_text()
            message += title + newline
            sub_count += 1

if sub_count > 0:
    sns = boto3.client('sns')
    response = sns.publish(TopicArn='arn:aws:sns:ap-northeast-1:127078038489:aws_cli_2', Message=message)
    print(response)

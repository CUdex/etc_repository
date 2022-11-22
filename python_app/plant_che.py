import re
from random import randint

category = ['살균제', '살충제', '제초제']
che_list = {'살균제': ['아이소프로티올레인','카벤다짐','트리사이클라졸','피라클로스트로빈','가스가마이신','아족시스트로빈','옥솔린산','클로로탈로닐','다조멧','디티아논','비터타놀','스트렙토마이신','이프로디온','크레속심메틸','펜사이큐론','폴펫','풀루아지남','플루퀸코나졸','메탈락실','캡탄','코퍼하이드록사이드','티오파네이트메틸','프로사이미돈','코퍼설페이트베이식','만코제브','메티람','폴리옥신디','프로클로라즈','프로피네브','디메토모르프','베노밀','사이목사닐','에트리디아졸','이프로벤포스','테부코나졸','파목사돈','폴리옥신비','헥사코나졸','황'],
'살충제': ['메틸브로마이드','아조사이클로틴','알루미늄포스파이드','카보퓨란','디메토에이트','펜토에이트','사이퍼메티린','사이헥사틴','스피로테트라맷','카바릴','클로르피리포스','포레이트','에토(아토)펜프프록스','에토(아토)프로포스','이미다클로프리드','카보설판','아세페이트','다이아지논','뷰프로페진','설폭사플로르','아이소프로카브','카두사포스','페노뷰카브','포스파미돈','아세타미프리드','디노테퓨란','메트알데하이드','페니트로티온','펜발러레이트','피리프록시펜','델타메트린','카탑하이드로클로라이드'],
'제초제': ['벤타존','에탈플루랄린','메톨라클로르','디캄바','메코프로프','옥사디아존','글루포시네이트암모늄','디클로베닐','뷰타클로르','펜디메탈린','MCPA','나프로파마이드','2,4-D','에스프로카브','글리포세이트이소프로팔아민','이사-디에틸에스터']}
question_queue = []

def check_question(question) -> bool:
    if question in question_queue:
        return True
    
    question_queue.append(question)
    
    if len(question_queue) > 10:
        question_queue.pop(0)
    
    return False

def question():
    choice_num = randint(0, 2)
    choice_category = category[choice_num]
    return (choice_num, che_list[choice_category][randint(0, len(che_list[choice_category]) - 1)])

def search_che(word):
    re_word = []
    match_word = re.compile(f'{word}')

    for key, value in che_list.items():
        for che in value:
            if match_word.search(che):
                re_word.append(che)
        print(f"{key}: {re_word}")
        re_word = []

print("""
농약 문제입니다.
농약의 이름이 하나 제시가 되면 이 이름을 확인하고
1. 살균제, 2. 살충제, 3. 제초제 중 하나를 선택하여 번호를 입력합니다.
만약 3가지 카테고리 중 특정 단어가 포함된 것이 있는지 확인하려면 4번을 입력하고 엔터를 눌러주세용
""")
while True:
    print("----------------------------------------------------------")
    answer = question()

    while check_question(answer[1]):
        answer = question()

    answer_category = answer[0]
    answer_che = answer[1]

    print(f'다음 농약은 어떤 종류의 농약일까요?:  $$$ {answer_che} $$$')
    request_category = input('1. 살균제, 2. 살충제, 3. 제초제 중 하나를 선택하여 번호를 입력합니다.(4. 검색, 5. 종료): ')

    if str(answer_category + 1) == request_category:
        print("""
ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
ooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
        """)
        print(f"{category[answer_category]} 정답입니다.")
    elif request_category == '4':
        word = input("검색을 원하는 단어를 입력하세요: ")
        search_che(word)
    elif request_category == '5':
        break
    else:
        print("""
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
""")
        print(f"정답은 {category[answer_category]}입니다.")

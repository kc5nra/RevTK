<?php

/*
 * This file is part of the Reviewing the Kanji package.
 * Copyright (c) 2005-2010  Fabrice Denis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class gives information about the RtK book such as
 *  number of lessons, kanji count per lesson, number of kanji in book 1 and book 3, etc.
 *  
 *  isValidRtkFrameNum($num)
 *  getIndexForKanji($cjk)               Returns frame number 1 to 3007 or FALSE
 *  getKanjiForIndex($id)
 *  getNumLessons()
 *  getLessons()
 *  getKanjiCountForLesson($lesson)
 *  getLessonForFramenum($framenum)
 *  getProgressSummary($cur_framenum)
 * 
 * @package RevTK
 * @author  Fabrice Denis
 */

class rtkBook
{
	/**
	 * Kanji count for Remembering the Kanji Volume 1
	 */
	const MAXKANJI_VOL1 = 2042;

	/**
	 * Kanji count for Remembering the Kanji Volume 1 + 3
	 */
	const MAXKANJI_VOL3 = 3007;
		
	/**
	 * Separator between multiple edition keywords (as stored in KanjisPeer table)
	 * eg. 'village/town'
	 */
	const EDITION_SEPARATOR = '/';

	public static
		$rtk1Lessons = array(
1 => 15, 2 => 19, 3 => 18, 4 => 18, 5 => 24, 6 => 10, 7 => 22, 8 => 46, 9 => 22, 10 => 40, 11 => 15, 12 => 27, 13 => 23, 
14 => 24, 15 => 29, 16 => 17, 17 => 26, 18 => 80, 19 => 33, 20 => 6, 21 => 63, 22 => 59, 23 => 130, 24 => 29, 25 => 96, 
26 => 59, 27 => 76, 28 => 18, 29 => 41, 30 => 39, 31 => 59, 32 => 36, 33 => 28, 34 => 46, 35 => 39, 36 => 62, 37 => 32, 
38 => 57, 39 => 47, 40 => 56, 41 => 29, 42 => 32, 43 => 34, 44 => 28, 45 => 47, 46 => 19, 47 => 30, 48 => 22, 49 => 25, 
50 => 27, 51 => 24, 52 => 23, 53 => 51, 54 => 28, 55 => 20, 56 => 17
		);

	/**
	 * All kanjis in RtK Vol.1 and Vol.3 (3007 characters)
	 * Ordered by index number, so that the character offset in the string maps to heisig indexes.
	 */
	public static
		$kanjis = '一二三四五六七八九十口日月田目古吾冒朋明唱晶品呂昌早旭世胃旦胆亘凹凸旧自白百中千舌升昇丸寸専博占上下卓朝只貝貞員見児元頁頑凡負万句肌旬勺的首乙乱直具真工左右有賄貢項刀刃切召昭則副別丁町可頂子孔了女好如母貫兄克小少大多夕汐外名石肖硝砕砂削光太器臭妙省厚奇川州順水氷永泉原願泳沼沖江汁潮源活消況河泊湖測土吐圧埼垣圭封涯寺時均火炎煩淡灯畑災灰点照魚漁里黒墨鯉量厘埋同洞胴向尚字守完宣宵安宴寄富貯木林森桂柏枠梢棚杏桐植枯朴村相机本札暦案燥未末沫味妹朱株若草苦寛薄葉模漠墓暮膜苗兆桃眺犬状黙然荻狩猫牛特告先洗介界茶合塔王玉宝珠現狂皇呈全栓理主注柱金銑鉢銅釣針銘鎮道導辻迅造迫逃辺巡車連軌輸前各格略客額夏処条落冗軍輝運冠夢坑高享塾熟亭京涼景鯨舎周週士吉壮荘売学覚栄書津牧攻敗枚故敬言警計獄訂討訓詔詰話詠詩語読調談諾諭式試弐域賊栽載茂成城誠威滅減桟銭浅止歩渉頻肯企歴武賦正証政定錠走超赴越是題堤建延誕礎婿衣裁装裏壊哀遠猿初布帆幅帽幕幌錦市姉肺帯滞刺制製転芸雨雲曇雷霜冬天橋嬌立泣章競帝童瞳鐘商嫡適滴敵匕北背比昆皆混渇謁褐喝旨脂壱毎敏梅海乞乾腹複欠吹炊歌軟次茨資姿諮賠培剖音暗韻識鏡境亡盲妄荒望方妨坊芳肪訪放激脱説鋭曽増贈東棟凍妊廷染燃賓歳県栃地池虫蛍蛇虹蝶独蚕風己起妃改記包胞砲泡亀電竜滝豚逐遂家嫁豪腸場湯羊美洋詳鮮達羨差着唯焦礁集准進雑雌準奮奪確午許歓権観羽習翌曜濯曰困固国団因姻園回壇店庫庭庁床麻磨心忘忍認忌志誌忠串患思恩応意想息憩恵恐惑感憂寡忙悦恒悼悟怖慌悔憎慣愉惰慎憾憶慕添必泌手看摩我義議犠抹抱搭抄抗批招拓拍打拘捨拐摘挑指持括揮推揚提損拾担拠描操接掲掛研戒械鼻刑型才財材存在乃携及吸扱丈史吏更硬又双桑隻護獲奴怒友抜投没設撃殻支技枝肢茎怪軽叔督寂淑反坂板返販爪妥乳浮将奨採菜受授愛払広拡鉱弁雄台怠治始胎窓去法会至室到致互棄育撤充銃硫流允唆出山拙岩炭岐峠崩密蜜嵐崎入込分貧頒公松翁訟谷浴容溶欲裕鉛沿賞党堂常裳掌皮波婆披破被残殉殊殖列裂烈死葬瞬耳取趣最撮恥職聖敢聴懐慢漫買置罰寧濁環還夫扶渓規替賛潜失鉄迭臣姫蔵臓賢堅臨覧巨拒力男労募劣功勧努励加賀架脇脅協行律復得従徒待往征径彼役徳徹徴懲微街衡稿稼程税稚和移秒秋愁私秩秘称利梨穫穂稲香季委秀透誘穀菌米粉粘粒粧迷粋糧菊奥数楼類漆様求球救竹笑笠笹筋箱筆筒等算答策簿築人佐但住位仲体悠件仕他伏伝仏休仮伯俗信佳依例個健側侍停値倣倒偵僧億儀償仙催仁侮使便倍優伐宿傷保褒傑付符府任賃代袋貸化花貨傾何荷俊傍久畝囚内丙柄肉腐座卒傘匁以似併瓦瓶宮営善年夜液塚幣弊喚換融施旋遊旅勿物易賜尿尼泥塀履屋握屈掘堀居据層局遅漏刷尺尽沢訳択昼戸肩房扇炉戻涙雇顧啓示礼祥祝福祉社視奈尉慰款禁襟宗崇祭察擦由抽油袖宙届笛軸甲押岬挿申伸神捜果菓課裸斤析所祈近折哲逝誓暫漸断質斥訴昨詐作雪録尋急穏侵浸寝婦掃当争浄事唐糖康逮伊君群耐需儒端両満画歯曲曹遭漕槽斗料科図用庸備昔錯借惜措散廿庶遮席度渡奔噴墳憤焼暁半伴畔判券巻圏勝藤謄片版之乏芝不否杯矢矯族知智矛柔務霧班帰弓引弔弘強弱沸費第弟巧号朽誇汚与写身射謝老考孝教拷者煮著署暑諸猪渚賭峡狭挟追師帥官棺管父交効較校足促距路露跳躍践踏骨滑髄禍渦過阪阿際障随陪陽陳防附院陣隊墜降階陛隣隔隠堕陥穴空控突究窒窃窪搾窯窮探深丘岳兵浜糸織繕縮繁縦線締維羅練緒続絵統絞給絡結終級紀紅納紡紛紹経紳約細累索総綿絹繰継緑縁網緊紫縛縄幼後幽幾機玄畜蓄弦擁滋慈磁系係孫懸却脚卸御服命令零齢冷領鈴勇通踊疑擬凝範犯厄危宛腕苑怨柳卵留貿印興酉酒酌酵酷酬酪酢酔配酸猶尊豆頭短豊鼓喜樹皿血盆盟盗温監濫鑑猛盛塩銀恨根即爵節退限眼良朗浪娘食飯飲飢餓飾館養飽既概慨平呼坪評刈希凶胸離殺純鈍辛辞梓宰壁避新薪親幸執報叫糾収卑碑陸睦勢熱菱陵亥核刻該劾述術寒醸譲壌嬢毒素麦青精請情晴清静責績積債漬表俵潔契喫害轄割憲生星姓性牲産隆峰縫拝寿鋳籍春椿泰奏実奉俸棒謹勤漢嘆難華垂睡錘乗剰今含吟念琴陰予序預野兼嫌鎌謙廉西価要腰票漂標栗遷覆煙南楠献門問閲閥間簡開閉閣閑聞潤欄闘倉創非俳排悲罪輩扉侯候決快偉違緯衛韓干肝刊汗軒岸幹芋宇余除徐叙途斜塗束頼瀬勅疎速整剣険検倹重動勲働種衝薫病痴痘症疾痢疲疫痛癖匿匠医匹区枢殴欧抑仰迎登澄発廃僚寮療彫形影杉彩彰彦顔須膨参惨修珍診文対紋蚊斉剤済斎粛塁楽薬率渋摂央英映赤赦変跡蛮恋湾黄横把色絶艶肥甘紺某謀媒欺棋旗期碁基甚勘堪貴遺遣舞無組粗租祖阻査助宜畳並普譜湿顕繊霊業撲僕共供異翼洪港暴爆恭選殿井囲耕亜悪円角触解再講購構溝論倫輪偏遍編冊典氏紙婚低抵底民眠捕浦蒲舗補邸郭郡郊部都郵邦郷響郎廊盾循派脈衆逓段鍛后幻司伺詞飼嗣舟舶航般盤搬船艦艇瓜弧孤繭益暇敷来気汽飛沈妻衰衷面革靴覇声呉娯誤蒸承函極牙芽邪雅釈番審翻藩毛耗尾宅託為偽長張帳脹髪展喪巣単戦禅弾桜獣脳悩厳鎖挙誉猟鳥鳴鶴烏蔦鳩鶏島暖媛援緩属嘱偶遇愚隅逆塑岡鋼綱剛缶陶揺謡就懇墾免逸晩勉象像馬駒験騎駐駆駅騒駄驚篤騰虎虜膚虚戯虞慮劇虐鹿薦慶麗熊能態寅演辰辱震振娠唇農濃送関咲鬼醜魂魔魅塊襲嚇朕雰箇錬遵罷屯且藻隷癒丹潟丑卯巳此柴砦些髭璃禽檎憐燐麟鱗奄庵掩俺悛駿峻竣臼舅鼠鑿毀艘犀皐脊畷綴爾璽鎧凱妖沃呑韮籤懺芻雛趨尤稽厖采或斬兎也尭巴甫疋菫曼巾云卜喬莫倭侠倦佼俄佃伶仔仇伽僅僻儲倖僑侶伎侃倶侭佑俣傭偲脩倅做凄冴凋凌冶凛凧凪夙鳳劉刹剥剃匂勾厭雁贋厨仄哨嘲咎囁喋咽嘩噂咳喧喉唾叩嘘啄呪吠吊噛叶吻吃噺噌唄叱邑呆喰埴坤堆壕垢坦埠填堰堵嬰姦妬婢婉娼妓娃姪嫉嬬姥姑姐嬉孕孜宥寓宏牢塞宋宍屠屁屑尻屡屍屏嵩崚峨崖嶺嵌嵯帖幡幟庖廓庇鷹庄廟彊弥弛粥挽撞扮掠挨掴捺捻掻撰拭揃捌撹摺按捉拶播揖托捧撚挺擾捗撫撒擢捷抉怯惟惚怜惇憧恰恢悌湧澪洸滉漱洲洵滲洒沐泪渾沙涜淫梁澱氾洛汝漉瀕濠溌溺湊淋浩汀鴻潅溢汰湛淳潰渥灘汲瀞溜渕沌汎濾濡淀涅釜斧爺猾猥狡狸狼狽狗狐狛狙獅狒莨茉莉苺萩藝薙蓑萎苔蕩蔽蔓蓮芙蓉蘭芦薯菖蕉芯蕎蕗藍茄苛蔭蓬芥萌葡萄蘇蕃苓菰蒙茅芭苅蓋葱蔑葵葺蕊茸蒔芹苫葛蒼藁蕪藷薮蒜蕨蔚茜莞蒐菅葦迪辿這迂遁逢遥遼逼迄遜逗郁鄭隙隈憑惹悉忽惣愈恕昴晋曖晟暈暉旱晏晨晒昧晃曝曙昂旺昏晦腎股膿腑胱胚肛臆膝脆肋肘腔腺腫膳肱胡楓枕楊椋榛櫛槌樵梯椅柿柑桁杭柊柚椀栂柾榊樫槙楢橘桧棲栖梗桔杜杷梶杵杖椎樽柵櫓橿杓李棉楯榎樺槍柘梱枇樋橇槃栞椰檀樗槻椙彬桶楕樒毬燿燎炬焚灸燭煽煤煉燦灼烙焔熔煎烹牽牝牡瑶琳瑠斑琉弄瑳琢珊瑚瑞珪玖瑛玩玲畏畢畦痒痰疹痔癌痩痕痺眸眩瞭眉雉矩磐碇碧硯砥碗碍碩磯砺碓禦祷祐祇祢禄禎秤黍禿稔稗穣稜稀穆窺窄窟穿竃竪颯站靖妾衿裾袷袴襖笙筏簾箪竿箆箔笥箭筑篭篠箸纂竺箕笈篇筈簸粕糟糊籾糠糞粟繋綸絨絆緋綜紐紘纏絢繍紬綺綾絃綻縞綬紗舵舷聯聡聘耽耶蚤蟹蛋蟄蝿蟻蜂蝋蝦蛸螺蝉蛙蛾蛤蛭蛎罫罵袈裟戴截哉詢諄讐諌謎諒讃誰訊訣詣諦詮詑誼謬詫諏諺誹謂諜註譬轟輔輻輯貌豹賎貼貰賂賑躓蹄蹴蹟跨跪醤醍酎醐醒醇麺麹釦銚鋤鍋鏑鋸錐鍵鍬鋲錫錨釘鑓鋒鎚鉦錆鍾鋏閃悶閤闇雫霞翰斡鞍鞭鞘鞄靭鞠頓顛穎頃頬頗頌顎頚餌餐饗蝕飴餅駕騨馳騙馴駁駈驢鰻鯛鰯鱒鮭鮪鮎鯵鱈鯖鮫鰹鰍鰐鮒鮨鰭鴎鵬鸚鵡鵜鷺鷲鴨鳶梟塵麓麒冥瞑暝坐挫朔遡曳洩彗慧嘉兇兜爽欝劫勃歎輿巽歪翠黛鼎鹵鹸虔燕嘗殆孟牌骸覗彪秦雀隼耀夷戚嚢丼暢廻畿欣毅斯匙匡肇麿叢肴斐卿翫於套叛尖壷叡酋鴬赫臥甥瓢琵琶叉舜畠拳圃丞亮胤疏膏魁馨牒瞥阜睾巫敦奎翔皓黎赳已棘聚甦剪躾夥鼾祟粁糎粍噸哩浬吋呎梵陀薩菩唖迦那牟珈琲檜轡淵伍什萬邁逞燈裡薗鋪嶋峯巌埜舘龍寵聾慾亙躯嶽國脛勁箋祀祓躇壽躊彙饅嘔鼈亨侑梧欽煕而掟';

	/**
	 * 
	 * @param  int  $num  A number
	 * 
	 * @return bool  True if number is valid Rtk frame number
	 */
	public static function isValidRtkFrameNum($num)
	{
		return ($num >= 1 && $num <= self::MAXKANJI_VOL3);
	}

	/**
	 * Returns framenumber (from 1 to 3007, cf. rtkBook) for a kanji character.
	 * 
	 * @param  string  $cjk   A single utf8 character
	 * @return mixed   Framenumber or false if the cjk character is not in Heisig
	 */
	public static function getIndexForKanji($cjk)
	{
		return mb_strpos(self::$kanjis, $cjk, 0, 'utf8') + 1;
	}
	
	/**
	 * Returns a kanji (utf8 character) for a valid RtK frame number.
	 * 
	 * @param  int     A RTK frame number (starts at 1)
	 * 
	 * @return string
	 */
	public static function getKanjiForIndex($id)
	{
		return mb_substr(self::$kanjis, $id - 1, 1, 'utf8');
	}

	/**
	 * Returns number of lessons in RtK Vol.1
	 * 
	 * @param  
	 * @return 
	 */
	public static function getNumLessons()
	{
		return count(self::$rtk1Lessons);
	}

	/**
	 * Returns lessons indexed by number with kanji count for each lesson.
	 * 
	 * @param  
	 * @return 
	 */
	public static function getLessons()
	{
		return self::$rtk1Lessons;
	}

	/**
	 * Returns count of kanji in given lesson.
	 * 
	 * @param  int $lesson   Lesson starts at 1 like in the book.
	 * @return mixed   Count or false
	 */
	public static function getKanjiCountForLesson($lesson)
	{
		if (isset(self::$rtk1Lessons[$lesson]))
		{
			return self::$rtk1Lessons[$lesson];
		}

		return false;
	}
	
	/**
	 * Returns lesson number given frame number.
	 * 
	 * @param
	 * @return int
	 */
	public static function getLessonForFramenum($framenum)
	{
		$lesson = 0;
		if ($framenum > 0 && $framenum <= self::MAXKANJI_VOL1)
		{
			$maxframe = 0;
			foreach (self::$rtk1Lessons as $lesson => $count)
			{
				$maxframe += $count;
				if ($framenum <= $maxframe)
				{
					break;
				}
			}
		}
		elseif ($framenum <= self::MAXKANJI_VOL3)
		{
			$lesson = self::getNumLessons() + 1;
		}
		return $lesson;
	}

	/**
	 * Return summary progress for member
	 * 
	 * Returns object:
	 *  ->curLessonNum
	 *  ->kanjiToGo
	 * 
	 * @return mixed  Progress info as an object, or null if RtK Vol 1. is completed.
	 */
	public static function getProgressSummary($cur_framenum)
	{
		if ($cur_framenum >= self::MAXKANJI_VOL1)
		{
			return null;
		}

		$progress = new stdClass;
		$maxframe = 0;
		foreach(self::$rtk1Lessons as $lesson => $kanjiCount)
		{
			$minframe = $maxframe + 1;
			$maxframe += $kanjiCount;
			if ($cur_framenum < $maxframe)
			{
				break;
			}
		}

		$progress->curLessonNum = $lesson;
		$progress->kanjiToGo = $kanjiCount - ($cur_framenum - $minframe + 1);
		return $progress;
	}
}

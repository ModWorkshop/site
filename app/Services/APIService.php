<?php

namespace App\Services;

use App\Http\Resources\AnnouncementResource;
use App\Models\Ban;
use App\Models\Game;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Supporter;
use App\Models\Suspension;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserCase;
use Arr;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\Password;
use Storage;
use Jcupitt\Vips;
use Log;
use Route;
use Str;

const animated = [
    'gif' => true,
    'webp' => true,
    'avif' => true,
    'jxl' => true
    // 'png' => true, https://github.com/libvips/libvips/issues/2537
    // 'apng' => true
];


const donationSites = [
    'bmc' => '/(?:https:\/\/)?(?:www\.)?buymeacoffee\.com\/(\w+)/',
    'kofi' => '/(?:https:\/\/)?(?:www\.)?ko-fi\.com\/(\w+)/',
    'paypalme' => '/(?:https:\/\/)?(?:www\.)?paypal\.me\/(\w+)/',
    'paypalBtn' => '/(?:https:\/\/)?(?:www\.)?paypal(?:\.me|\.com)\/donate\/\?hosted_button_id=(\w+)/',
    'github' => '/(?:https:\/\/)?(?:www\.)?github\.com\/sponsors\/(\w+)/'
];

class APIService {
    /**
     * Allows for appending into paginator items
     *
     * @param Paginator $paginator
     * @param string $key
     * @return void
     */
    public static function appendToItems(Paginator $paginator, string $key)
    {
        /**
         * @var Model[]
         */
        $items = $paginator->items();
        foreach ($items as $cat) {
            $cat->append($key);
        }
    }

    /**
     * The opposite of ConvertEmptyStringsToNull, this converst nulls we expect to be empty at times.
     * For example strings, if you send them as empty string, PHP doesn't know if it's null or empty string.
     *
     * @param array $arr
     * @param string $key
     * @return void
     */
    public static function nullToEmptyStr(array &$arr, string ...$keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                $arr[$key] ??= '';
            }
        }
    }

    /**
     * Converts nulls to empty array. This is useful for dealing with empty arrays in formdata
     *
     * @param array $arr
     * @param string ...$keys
     * @return void
     */
    public static function nullToEmptyArr(array &$arr, string ...$keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                $arr[$key] ??= [];
            }
        }
    }

    /**
     * Stores an UploadedFile $file into r2 storage
     *
     * @param UploadedFile|null $file
     * @param string $fileDir Where to store the image
     * @param string|null $oldFile Automatically delete old files including thumbnail
     * @param integer|null $thumbnailSize Make a thumbnail for the image
     * @param callable|null $onSuccess Callback to run after successful upload
     * @param boolean Whether to allow the file to be simply deleted if given an empty string
     * @return array
     */
    public static function storeImage(UploadedFile|string|null $file, string $fileDir, ?string $oldFile=null, array $config = [])
    {
        if (!isset($file)) {
            return null;
        }

        $img = '';
        if (strlen($file) > 0) {
            $opts = isset(animated[$file->extension()]) ? '[n=-1]' : '';
            $img = Vips\Image::newFromFile($file->path().$opts);
        }

        return self::storeImageByObject($img, $fileDir, $oldFile, $config);
    }

    public static function storeImageByObject(Vips\Image|string|null $img, string $fileDir, ?string $oldFile=null, array $config = []) {
        $config['allowDeletion'] ??= false;

        $isEmptyFile = strlen($img) == 0;
        if ((!$isEmptyFile || ($isEmptyFile && $config['allowDeletion'])) && isset($oldFile) && !str_contains($oldFile, 'http')) {
            $oldFile = preg_replace('/\?t=\d+/', '', $oldFile);
            Storage::delete($fileDir.'/'.$oldFile);
            Storage::delete($fileDir.'/thumbnail_'.$oldFile);
        }

        // Empty file means delete the file and that's it
        if ($isEmptyFile) {
            if ($config['allowDeletion'] && isset($config['onSuccess'])) {
                $config['onSuccess']('');
            }
            return null;
        }

        $fileName = Str::random(40).'.webp';

        if (isset($config['size'])) {
            $img = $img->thumbnail_image($config['size']);
        }

        $buffer = $img->writeToBuffer('.webp', ['Q' => 80]);
        Storage::put($fileDir.'/'.$fileName, $buffer);

        $thumb = null;
        $thumbBuffer = null;
        if (isset($config['thumbnailSize'])) {
            $thumb = $img->thumbnail_image($config['thumbnailSize']);
            $thumbBuffer = $thumb->writeToBuffer('.webp');
            Storage::put($fileDir.'/thumbnail_'.$fileName, $thumbBuffer);
        }

        if (isset($config['onSuccess'])) {
            $config['onSuccess']($fileName);
        }

        $ret = [
            'image' => $img,
            'thumbnail' => $thumb,
            'name' => $fileName,
            'size' => strlen($buffer),
            'type' => 'webp'
        ];

        if (isset($thumbBuffer)) {
            $ret['thumbnail_size'] = strlen($thumbBuffer);
        }

        return $ret;
    }

    /**
     * Deletes an image from the storage
     */
    public static function deleteImage(string $fileDir, ?string $file=null) {
        if (isset($file) && !str_contains($file, 'http')) {
            $file = preg_replace('/\?t=\d+/', '', $file);
            Storage::delete($fileDir.'/'.$file);
            Storage::delete($fileDir.'/thumbnail_'.$file);
        }
    }

    public static function report(Request $request, Model $model)
    {
        $val = $request->validate([
            'reason' => 'string|min:3|max:1000'
        ]);

        $model->report($val['reason']);
    }

    public static function getUnseenNotifications()
    {
        $userId = Auth::user()?->id;

        if (!isset($userId)) {
            return null;
        }

        return Notification::where('user_id', $userId)->where('seen', false)->count();
    }

    public static function getAnnouncements(Game $game=null)
    {
        $announcements = Thread::where('forum_id', isset($game) ? $game->forum_id : 1)->where('announce', true)->get();

        $now = Carbon::now();
        foreach ($announcements as $annoucement) {
            if (isset($annoucement->announce_until) && $now->greaterThan($annoucement->announce_until)) {
                $annoucement->update(['announce' => false]);
            }
        }

        return AnnouncementResource::collection($announcements->take(2));
    }

    public static function currentGame(): ?Game
    {
        return app('siteState')->getCurrentGame();
    }

    public static function setCurrentGame(?Game $game)
    {
        app('siteState')->setCurrentGame($game);
    }

    public static function getSettings()
    {
        $query = Setting::query();

        $user = Auth::user();
        if (!$user?->hasPermission('admin')) {
            $query->where('public', true);
        }

        return Arr::pluck($query->get(), 'value', 'name');
    }

    public static function getPasswordRule()
    {
        return Password::min(12)->numbers()->mixedCase();
    }


    /**
     * Registers a game resource with also a direct resource link.
     * store method still requires a game so that's not available in the global one.
     */
    public static function resource(string $resource, string $class, string $parent, array $config=[]) {
        $reg = Route::resource("{$parent}.{$resource}", $class);
        if (isset($config['parentOptional']) && $config['parentOptional'] == true) {
            Route::resource($resource, $class)->only(['index', 'store']);
        } else {
            Route::resource($resource, $class)->only($config['selfOnly'] ?? ['index']);
        }
        if ($config['shallow'] ?? true) {
            $reg->shallow();
        }
        $reg->except(['create', 'edit', ...($config['except'] ?? [])]);

        return $reg;
    }
    public static function gameResource(string $resource, string $class, array $config=[]) {
        return self::resource($resource, $class, 'games', $config);
    }

    /**
     * Returns an array of 'data' and 'meta' (no links because that shit is a waste of space).
     * Meta contains current_page, last_page, total and per_page.
     */
    public static function paginatedResponse(ResourceCollection $collection)
    {
        $resource = $collection->resource;
        return [
            'data' => $collection,
            'meta' => [
                'current_page' => $resource->currentPage(),
                'from' => $resource->firstItem(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'to' => $resource->lastItem(),
                'total' => $resource->total(),
            ]
        ];
    }

    /**
     * Returns a hash string that is built off the current given query. Used in caching when the user doesn't affect the data.
     */
    public static function hashByQuery()
    {
        return md5(serialize(request()->getQueryString()));
    }

    public static function adminData(Game $game = null)
    {
        $arr = [];

        $moderateUsers = Auth::user()->hasPermission('moderate-users', $game);
        $manageMods = Auth::user()->hasPermission('manage-mods', $game);

        if (!$moderateUsers && !$manageMods) {
            abort(403);
        }

        $gameQuery = fn($q) => $q->whereGameId($game->id);
        $globalQuery = fn($q) => $q->whereNull('game_id');

        if ($moderateUsers) {
            $arr['user_cases'] = UserCase::with('modUser')
                ->orderByRaw('active DESC, created_at DESC')
                ->when(isset($game), $gameQuery, $globalQuery)
                ->limit(3)
                ->get();

            $arr['reports'] = Report::orderByDesc('created_at')
                ->when(isset($game), $gameQuery)
                ->limit(3)
                ->get();

            $arr['bans'] = Ban::with(['modUser', 'user'])
                ->orderByDesc('created_at')
                ->whereActive(true)
                ->when(isset($game), $gameQuery, $globalQuery)
                ->limit(3)
                ->get();
        }

        if ($manageMods) {
            $arr['suspensions'] = Suspension::with('mod')
                ->orderByDesc('created_at')
                ->when(isset($game), function($q) use($game) {
                    $q->whereRelation('mod', fn($q) => $q->whereGameId($game->id));
                })
                ->whereStatus(true)
                ->limit(3)
                ->get();
        }

        return $arr;
    }

    public static function nitroCheck(User $user) {
        $signer = new \NitroPaySponsor\Signer(env('NITRO_TOKEN'));

        $user->nitroToken = $signer->sign([
            'siteId' => '92', // required
            'userId' => $user->id, // required
        ]);

        $subInfo = $signer->getUserSubscription($user?->id);
        $registeredSub = Supporter::where('provider', 'nitro')->where('user_id', $user->id)->first();

        if (!isset($registeredSub)) {
            if ($subInfo && $subInfo->status == 'active') {
                $registeredSub = Supporter::create([
                    'provider' => 'nitro',
                    'user_id' => $user->id
                ]);
            } else {
                return;
            }
        }

        $registeredSub->expire_date = Carbon::create($subInfo->subscribedUntil);
        $registeredSub->expired = $subInfo->status != 'active';
        $registeredSub->save();

        return $registeredSub;
    }

    public static function checkCaptcha(Request $request) {
        if (app()->isProduction()) {
            $request->validate([
                'h-captcha-response' => ['required', 'hcaptcha'],
            ], [
                'h-captcha-response' => 'You must solve the captcha to do this action!'
            ]);
        }
    }

    /**
     * Checks string against some cases
     *
     * If this string contains more than 3 links, it returns true.
     * If it detcts more than two hashtags, it returns true.
     * If it detects any of the blocked phrases and words from a huge list, it returns true.
     * If it detects a vietnamese phone number, it return true.
     *
     * As time goes this should be updated with more patterns from these spammers.
     * This shouldn't be ran on trusted users! We don't wish to censor users.
     *
     * @param string $str
     * @return bool
     */
    public static function checkSpamContent(string $str) {
        // Annoying, but you can't have too many links
        if (self::countLinks($str) > 3) {
            return true;
        }

        // There's very little reason to use hashtags on mws, most of the spammers use them
        $hashTags = preg_match_all('/(#[a-zA-Z0-9_.]+)/', $str);
        if ($hashTags > 2) {
            return true;
        }

        if (self::containsSpammyWords($str)) {
            return true;
        }

        $matchPhoneNumbers = preg_match('/\+?(84|0)?[1-9][0-9]{7,14}/', $str);
        if ($matchPhoneNumbers) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the string contains spammy words
     * @param string $str
     * @return bool
     */
    public static function containsSpammyWords(string $str) {
        // YES this list is generated by ChatGPT xd
        // I am aware this is kinda shit, but look this is only ran for very new users which covers 99% of spammers
        $blockedWords = [
            // Vietnamese
            'cá cược', 'nhà cái', 'trang chủ', 'ưu đãi', 'khuyến mãi', 'nổ hũ', 'trò chơi', 'đổi thưởng', 'bóng đá',
            'game bài', 'chính thức', 'tài khoản', 'xổ số', 'thể thao', 'đặc biệt', 'tỷ lệ cược', 'đăng ký',
            'chương trình', 'sự kiện', 'chính sách', 'liên hệ', 'nạp tiền', 'rút tiền', 'cược ngay', 'tiền thưởng',
            'trải nghiệm', 'giải trí', 'chơi ngay', 'nhận thưởng', 'hoàn trả', 'đá gà', 'bắn cá', 'lô đề', 'miễn phí',
            'tặng ngay', 'bảo mật tuyệt đối', 'thắng lớn', 'đăng nhập', 'khuyến mãi hot', 'tặng code', 'ưu đãi độc quyền',
            'soi cầu', 'chốt số', 'phân tích xổ số', 'lô kép', 'cặp số vàng', 'kết quả xổ số', 'lô tô',
            'nha cai', 'ca cuoc the thao', 'no hu', 'xo so', 'casino truc tuyen',
            'trai nghiem giai tri', 'an toan va minh bach', 'chinh sach than thien',
            'san choi van minh', 'dang trai nghiem nhat', 'nha cai uy tin',
            'he thong ca cuoc hien dai', 'cam ket mang lai', 'tinh nang ho tro',
            'dia chi', 'ho chi minh', 'viet nam', 'SDT', 'so dien thoai', 'lien he',
            'don vi ca cuoc', 'truc tuyen dinh cao', 'tua game doi thuong',
            'ca cuoc truc tuyen', 'san choi ca cuoc', 'nha cai uy tin', 'game ca cuoc',
            'trai nghiem ca cuoc', 'tro choi doi thuong', 'game bai', 'doi thuong hap dan',
            'ca cuoc the thao', 'casino truc tuyen', 'gop phan', 'nha cai dinh cao',

            'Dự án Biệt Thự', 'Shophouse', 'Nhà Phố', 'Hồng Hạc Phú Mỹ Hưng City',
            'quy hoạch theo tiêu chuẩn', 'không gian sống lý tưởng',
            'cơ hội đầu tư hấp dẫn', 'kiến trúc sang trọng', 'cảnh quan thiên nhiên xanh mát',
            'điểm đến lý tưởng', 'an cư đẳng cấp', 'cơ hội kinh doanh sinh lời cao',
            'mở bán đợt mới', 'giá ưu đãi', 'bàn giao sổ đỏ', 'đầu tư bất động sản',
            'dự án tiềm năng', 'chủ đầu tư uy tín', 'mua ngay', 'sổ hồng riêng',
            'giá chỉ từ', 'thanh toán linh hoạt', 'đặc quyền sở hữu', 'tài sản sinh lời',
            'bất động sản cao cấp', 'đất nền Bắc Ninh', 'nhà phố thương mại', 'dự án hot',

            'Link Vào', 'Siêu Tốc', 'An Toàn Nhất', 'Link cá cược',
            'Trang chủ nhà cái', 'Tỷ lệ kèo', 'Soi kèo', 'Dự đoán bóng đá', 'Cá cược thể thao',
            'Nạp tiền cược', 'Rút tiền nhanh', 'Thưởng nóng', 'Game bài đổi thưởng',
            'Lô đề online', 'Tài xỉu', 'Slot đổi thưởng', 'Sòng bạc trực tuyến', 'Chơi ngay',
            'Nhận thưởng', 'Đăng ký nhận code', 'Nổ hũ', 'Kèo trên kèo dưới', 'Nhận code miễn phí',
            'Kết quả xổ số', 'Mẹo cá cược', 'Nhà cái uy tín', 'Tiền thưởng miễn phí',
            'Chương trình khuyến mãi', 'Hoàn trả cược', 'Tiền cược miễn phí', 'Vòng quay may mắn',
            'Nhận code VIP', 'Đá gà trực tuyến', 'Xóc đĩa', 'Bắn cá đổi thưởng',

            'vệ sinh môi trường', 'hút bể phốt', 'thông tắc cống', 'nạo vét hố ga',
            'thông tắc bồn cầu', 'hút hầm cầu', 'hút bùn', 'khử mùi hôi',
            'xử lý chất thải', 'máy hút chân không', 'dịch vụ vệ sinh',
            'thông tắc nhanh chóng', 'không đục phá', 'bảo hành dài hạn', 'dịch vụ 24/7',
            'hỗ trợ khách hàng', 'dịch vụ giá rẻ', 'cam kết xử lý triệt để', 'xe chuyên dụng',

            // Gambling Platform Names
            'NN88', '23WIN', '789bet', '123b', 'Kubet', 'Go88', 'Fi88', 'AE888', 'Hi88',
            'Rikvip', 'M88', 'W88', 'FB88', 'K8', 'May88', 'b52 club', 'i9bet', 'letou', 'j88',
            'dabet', 'f8bet', 's666', 'cmd368', 'vwin', 'new88', 'shbet', 'jbovietnam', 'thabet', 'sunwin',
            '88BET', '188BET',

            // Chinese
            '博彩', '下注', '真人娱乐', '彩金', '存款', '提款', '返水', '优惠', '高赔率', '独家优惠', '免费送彩金',
            '体育投注', '棋牌', '电子游戏', '六合彩', '百家乐', '官方指定', '财富自由', '投资回报',

            // Russian
            'букмекер', 'ставки на спорт', 'казино', 'бесплатный бонус', 'онлайн казино', 'игровые автоматы',
            'деньги онлайн', 'финансовая свобода', 'быстрый заработок', 'бонус файндер', 'бездепозитные бонусы',
            'актуальные бонусы', 'лучшие казино бонусы', 'онлайн казино Украина', 'играть без вложений',
            'получить бонус', 'активировать бонус', 'казино промокод', 'лучшие бездепозитные бонусы',
            'выиграй без вложений', 'казино с моментальным выводом', 'игровые автоматы бесплатно', 'слоты на гривны',
            'играть на деньги без вложений', 'проверенные казино', 'онлайн слоты Украина',

            // Inodnesian
            'Klasemen', 'Jadwal Pertandingan', 'Livescore', 'Berita Bola', 'Skor Langsung',
            'Prediksi Bola', 'Taruhan', 'Odds', 'Pasaran Bola', 'Bursa Taruhan',
            'Tebak Skor', 'Tips Taruhan', 'Parlay', 'Mix Parlay', 'Handicap',
            'Keuntungan Taruhan', 'Hasil Pertandingan', 'Live Odds', 'Bandar Bola',
            'Judi Bola', 'Prediksi Skor', 'Agen Bola', 'Taruhan Online',
            'Menang Banyak', 'Deposit', 'Withdraw', 'Bonus Taruhan', 'Hadiah Taruhan',

            // Thai
            'การพนัน', 'คาสิโนออนไลน์', 'เดิมพัน', 'หวยออนไลน์', 'เครดิตฟรี', 'แทงบอล', 'สมัครสมาชิกฟรี',

            // Spanish
            'apuestas', 'casino online', 'ganar dinero rápido', 'oferta exclusiva', 'promoción especial',
            'bono de bienvenida', 'tragamonedas', 'dinero fácil',

            // French
            'paris sportifs', 'casino en ligne', 'offre exclusive', 'crypto-monnaie', 'revenus passifs',
            'jeux d’argent', 'argent rapide',

            // Turkish
            'bahis', 'canlı bahis', 'iddaa', 'casino bonusu', 'slot oyunu', 'rulet', 'poker turnuvası',
            'jackpot kazancı', 'blackjack', 'bedava bahis', 'yüksek oranlar', 'tutan bahis', 'kesin maç',
            'canlı bahis ipuçları', 'risk içermeyen bahis', 'tahmin garantili', 'bedava döndürme',
            'yatırımsız bonus', 'anında ödeme', 'sınırsız kazanç', 'kazanç garantili', 'şans oyunu',
            'yüksek limitli bahis', 'bonus kampanyası', 'ücretsiz kupon', 'kupon kodu', 'gizli bahis',
            'yatırımla kazanç', 'banko maçlar', 'vip tahminler', 'düşük riskli bahis',

            // Arabic
            'شركة', 'نقل', 'اثاث', 'بالجبيل', 'نقل العفش', 'خدمات النقل', 'شحن', 'تفكيك',
            'تركيب', 'تغليف', 'عروض النقل', 'نقل الاثاث', 'نقل الأثاث', 'توصيل', 'مستودعات',
            'شاحنات', 'نقل رخيص', 'افضل شركة نقل', 'نقل سريع', 'الرياض', 'الدمام', 'جدة',
            'خصم', 'تخفيضات', 'عروض خاصة', 'اتصل بنا', 'خدمة العملاء', 'دليل الشركات',
            'خدمة 24 ساعة', 'شركة مضمونة', 'طلب عرض سعر', 'توصيل مجاني',

            // Japanese
            '市場調査', 'コンテンツ マネージャー', '市場分析', 'ビジネス', '高品質',
            'レポート', '業界調査', '顧客満足', '戦略', 'サービス', '無料相談',
            'プロフェッショナル', 'お問い合わせ', '資料請求', '割引', '特別オファー',
            '今すぐ購入', '無料見積もり', 'マーケティング', '最安値', '企業向け',
            'コンサルティング', 'お得情報', 'オンラインサービス', '世界最高',
            '独自技術', 'お試しキャンペーン', '成長市場', '無料体験', '市場レポート',
            'お客様のニーズ', '業界最高', 'トップクラス', '専門家', '収益向上',

            // Polish
            'Aborcja', 'klinika aborcyjna', 'przerywanie ciąży', 'zabieg aborcyjny',
            'zabieg w Czechach', 'legalna aborcja', 'aborcja na życzenie',
            'klinika ginekologiczna', 'opieka po zabiegu', 'bezpieczna aborcja',
            'medyczne usługi prywatne', 'leczenie za granicą', 'chirurgia plastyczna',
            'turystyka medyczna', 'operacje za granicą', 'zabiegi prywatne',
            'specjalistyczna opieka medyczna', 'zabieg bezpieczny', 'prywatna klinika',
            'profesjonalny sprzęt medyczny', 'dyskretny zabieg', 'transport pacjenta',
            'opieka pozabiegowa', 'gwarancja bezpieczeństwa', 'najwyższy komfort pobytu',

            // English
            'free bets', 'bet now', 'sports betting', 'live odds', 'casino bonus', 'roulette', 'slot machine',
            'poker tournament', 'jackpot', 'blackjack', 'wager', 'money back', 'betting odds',
            'high stakes', 'fixed matches', 'betting tips', 'risk-free bet', 'spread betting', 'pari-mutuel',
            'no deposit bonus', 'rollover requirement', 'online bookmaker', 'sportsbook', 'gambling site',
            'exclusive offer', 'free spins', 'big winnings', 'hot odds', 'bet slip', 'parlay bet', 'prop bet', 'cashout',
            'zipcode', 'online casino', 'casino bonus', 'casino betting', 'casino roulette', 'casino slot', 'discount',
            'deals', 'crypto', 'lottery',

            // Medical & Pharmaceutical Spam
            'viagra', 'cialis', 'levitra', 'pharmacy online', 'generic drugs', 'painkillers', 'weight loss pills',
            'anabolic steroids', 'testosterone boosters', 'growth hormone', 'erectile dysfunction', 'hair loss cure',
            'prescription drugs', 'no prescription needed', 'buy meds online', 'cheap medication',
            'anti-aging treatments', 'botox injections', 'sexual enhancement', 'increase testosterone',
            'HGH supplements', 'quick weight loss', 'diet pills', 'fast weight loss', 'natural male enhancement',
            'opioid pain relief', 'fentanyl', 'oxycontin', 'percocet', 'tramadol', 'adderall', 'valium', 'xanax',
            'modafinil', 'ritalin', 'smart drugs', 'cognitive enhancer', 'brain booster', 'memory enhancement',
            'hair regrowth formula', 'herbal remedies', 'detox supplements', 'miracle cure', 'FDA approved',
            'medical marijuana', 'CBD oil', 'anxiety medication', 'muscle relaxers', 'vape', 'nicotine', 'e-liquide',

            'nicotine pouches', 'tobacco-free', 'smoke-free', 'shop nicotine', 'buy nicotine pouches',
            'Zyn pouches', 'velo pouches', 'nordic spirit', 'nicotine alternatives', 'strong nicotine',
            'best nicotine pouches', 'quit smoking aid', 'snus', 'nicotine sachets', 'chewing tobacco',
            'nicotine gum', 'vape pods', 'vape juice', 'e-cigarettes', 'nicotine boosters',
            'mint nicotine', 'nicotine flavors', 'nicotine strengths', 'nicotine delivery', 'nicotine addiction',
            'iqos',

            'working as a', 'product manager in', 'for the last [0-9]+ years', 'company based in',
            'caters to the needs of its clients', 'valued for its exquisite products', 'intricate craftsmanship',
            'leading provider of', 'trusted brand in', 'our commitment to quality', 'satisfaction guaranteed',
            'shop now', 'check out our collection', 'order today', 'contact us for inquiries',
            'high-quality materials', 'expert craftsmanship', 'luxury at an affordable price',
        ];

        foreach ($blockedWords as $word) {
            if (stripos($str, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Counts how many links are in string
     *
     * @param string $message
     * @return int
     */
    public static function countLinks(string $str) {
        return preg_match_all('/(http|https|ftp|ftps):\/\/[^\s\/]+(?:\.[^\s\/]+)+(\/\S*)?/', $str);
    }

    public static function checkDonationLink(string $link) {
        if (!empty($link)) {
            if (preg_match(donationSites['kofi'], $link)) {
                return 'kofi';
            } elseif (preg_match(donationSites['bmc'], $link)) {
                return 'bmc';
            } elseif (preg_match(donationSites['paypalme'], $link)) {
                return 'paypalme';
            } elseif (filter_var($link, FILTER_VALIDATE_EMAIL)) {
                return 'paypal';
            } elseif (preg_match(donationSites['paypalBtn'], $link)) {
                return 'paypalBtn';
            } elseif (preg_match(donationSites['github'], $link)) {
                return 'github';
            }
        }
        return null;
    }
}

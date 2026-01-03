<?php
/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 * @link      https://github.com/DeveMain
 */

namespace App\Services\Fact;

use App\Models\Fact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Provides methods to generate, retrieve, and manage facts.
 */
class FactService
{
    /**
     * Creates a new instance.
     *
     * @param GroqService $groqService Service for AI-powered fact generation
     */
    public function __construct(
        protected readonly GroqService $groqService
    ) {}

    /**
     * Generate facts using AI or fallback to pre-defined facts.
     *
     * @param int $count Number of facts to generate (default: 10)
     * @return array Array of generated facts
     */
    public function generateFacts(int $count = 10): array
    {
        $factsArray = $this->groqService->generateFactsArray($count);

        if (empty($factsArray)) {
            return $this->createFallbackFacts($count);
        }

        $this->saveFacts($factsArray);

        return $factsArray;
    }

    /**
     * Save facts to database with upsert operation.
     *
     * @param array $facts Array of facts to save
     * @return array Array of saved facts
     */
    protected function saveFacts(array $facts): array
    {
        if (empty($facts)) {
            return [];
        }

        $now = Carbon::now();
        $prepared = array_map(
            fn (string $value): array => [
                'hash' => Fact::makeHash($value),
                'content' => trim($value),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $facts
        );

        return Fact::safeUpsert($prepared, ['hash'], ['content', 'updated_at']);
    }

    /**
     * Get fallback facts based on language.
     *
     * @param string $language Language code for facts (default: 'en')
     * @return array Array of fallback facts
     */
    protected function getFallbackFacts(string $language = 'en'): array
    {
        $fallbackFacts = [
            'en' => [
                'The first website, info.cern.ch, went online in 1991 and is still live today.',
                'The "Qwerty" keyboard layout was designed in the 1870s to slow typists down and prevent mechanical typewriter jams.',
                'Approximately 40% of the world\'s population uses social media, totaling over 3.2 billion people.',
                'The average human attention span online is now shorter than that of a goldfish, at about 8 seconds.',
                'The world\'s first computer programmer was Ada Lovelace, who wrote an algorithm for Charles Babbage\'s Analytical Engine in the 1840s.',
                'More than 90% of the world\'s data was created in just the last two years.',
                'The first email was sent by Ray Tomlinson in 1971; he chose the @ symbol to separate user from machine.',
                'A single Google search query requires about 1,000 computers to process in 0.2 seconds.',
                'The internet\'s physical structure includes over 1.2 million kilometers of submarine cables crisscrossing the ocean floor.',
                'The original name for "Wi-Fi" was "IEEE 802.11b Direct Sequence", and the term "Wi-Fi" itself doesn\'t actually stand for anything.',
                'The first-ever YouTube video, titled "Me at the zoo," was uploaded on April 23, 2005, and is 18 seconds long.',
                'There are more connected devices on Earth than there are people.',
                'CAPTCHA tests, which verify users are human, collectively waste over 500 human hours every day.',
                'The domain name "Business.com" was sold for $345 million in 2007, making it one of the most expensive ever.',
                'The first-ever tweet was sent by Jack Dorsey on March 21, 2006, and read: "just setting up my twttr".',
                'A single Bitcoin transaction uses roughly the same amount of electricity that an average U.S. household consumes in over 70 days.',
                'The iconic "save" icon is a floppy disk, a storage device most modern computer users have never physically used.',
                'The most expensive app ever developed was NASA\'s Mars Curiosity Rover app, costing nearly $25 million.',
                'The "404 Not Found" error code is named after a room at CERN where the original web servers were located.',
                'Technophobia, the fear of technology, is a recognized anxiety disorder.',
            ],
            'ru' => [
                'Первый в мире сайт info.cern.ch, созданный в 1991 году, до сих пор работает.',
                'Раскладка клавиатуры QWERTY была изобретена в 1870-х годах, чтобы замедлить печать и избежать заклинивания механических пишущих машинок.',
                'Около 40% населения Земли, или более 3.2 миллиарда человек, пользуются социальными сетями.',
                'Средняя продолжительность концентрации внимания человека в интернете — около 8 секунд, что меньше, чем у золотой рыбки.',
                'Первым в мире программистом была Ада Лавлейс, написавшая алгоритм для аналитической машины Чарльза Бэббиджа в 1840-х годах.',
                'Более 90% всех данных в мире было создано за последние два года.',
                'Первое электронное письмо отправил Рэй Томлинсон в 1971 году, используя символ @ для разделения имени пользователя и машины.',
                'Один поисковый запрос в Google обрабатывается примерно 1000 компьютерами за 0.2 секунды.',
                'Физическая структура интернета включает более 1.2 миллиона километров подводных кабелей, проложенных по дну океана.',
                'Изначально Wi-Fi назывался «IEEE 802.11b Direct Sequence», а сам термин «Wi-Fi» не является аббревиатурой и ничего не означает.',
                'Первое видео на YouTube под названием «Me at the zoo» было загружено 23 апреля 2005 года и длится 18 секунд.',
                'Количество подключенных к интернету устройств в мире превышает численность населения.',
                'Ежедневно человечество тратит более 500 часов в совокупности на решение капч (CAPTCHA).',
                'Доменное имя Business.com было продано в 2007 году за 345 миллионов долларов, став одним из самых дорогих в истории.',
                'Первый в истории твит отправил Джек Дорси 21 марта 2006 года: «just setting up my twttr».',
                'Одна транзакция с биткоином потребляет примерно столько же электроэнергии, сколько среднее домохозяйство в США за 70 с лишним дней.',
                'Значок «Сохранить» в виде дискеты — это устройство хранения данных, которым большинство современных пользователей никогда не пользовались.',
                'Самым дорогим приложением в истории стала программа для марсохода NASA Curiosity, стоившая почти 25 миллионов долларов.',
                'Код ошибки 404 «Not Found» назван в честь комнаты №404 в ЦЕРНе, где располагались первые веб-серверы.',
                'Технофобия, то есть боязнь технологий, является признанным тревожным расстройством.'
            ],
        ];

        return $fallbackFacts[$language] ?? $fallbackFacts['en'];
    }

    /**
     * Create fallback facts when AI is unavailable.
     */
    protected function createFallbackFacts(int $count = 10): array
    {
        $fallbackFacts = $this->getFallbackFacts();
        $fallbackFacts = array_slice($fallbackFacts, 0, min($count, count($fallbackFacts)));

        $this->saveFacts($fallbackFacts);

        return $fallbackFacts;
    }

    /**
     * Get the least shown fact (fair rotation).
     */
    public function getFreshFact(): ?Fact
    {
        if (!Fact::query()->exists()) {
            $this->createFallbackFacts(20);
        }

        $fact = Fact::query()
            ->orderBy('last_shown_at')
            ->orderBy('views')
            ->first();

        return $fact instanceof Fact ? $fact : null;
    }

    /**
     * Mark fact as shown and increment views.
     */
    public function markAsShown(Fact $fact): bool
    {
        $fact->views++;
        $fact->last_shown_at = Carbon::now();

        return $fact->save();
    }

    /**
     * Get a truly random fact.
     */
    public function getRandomFact(): ?Fact
    {
        $fact = Fact::query()->inRandomOrder()->first();

        return $fact instanceof Fact ? $fact : null;
    }

    /**
     * Get multiple random facts.
     */
    public function getRandomFacts(int $limit = 5): Collection
    {
        return Fact::query()
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}

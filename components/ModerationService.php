<?php

namespace app\components;

use Yii;
use app\models\User;

class ModerationService
{
    public static $badWords = [
        'дурак', 'идиот', 'дебил', 'кретин', 'тупой', 'глупый',
        'лох', 'придурок', 'урод', 'имбецил', 'дегенерат',
        'никчемный', 'бестолочь', 'недоумок', 'ублюдок',
        'чмо', 'дебилоид', 'тупица', 'балбес', 'олух',
        'козел', 'баран', 'осел', 'свинья', 'корова',
        'дешевка', 'шваль', 'отребье', 'быдло', 'сброд',
        'скотина', 'сволочь', 'мерзавец', 'гад', 'тварь',
        'подонок', 'отморозок', 'животное', 'выродок',
        'негодяй', 'изверг', 'паскуда', 'стерва', 'псих'
    ];
    
    public static function checkBan($userId)
    {
        $user = User::findOne($userId);
        if (!$user) {
            return ['is_banned' => false, 'message' => null];
        }
        
        if ($user->isBanned()) {
            return [
                'is_banned' => true,
                'message' => $user->ban_reason ?? 'Ваш аккаунт заблокирован'
            ];
        }
        
        return ['is_banned' => false, 'message' => null];
    }
    
    public static function checkText($text)
    {
        $found = [];
        $textLower = mb_strtolower($text);
        
        foreach (self::$badWords as $word) {
            if (mb_strpos($textLower, $word) !== false) {
                $found[] = $word;
            }
        }
        
        return $found;
    }
    
    public static function validateAndRecord($text, $userId, $commentId = null)
    {
        $foundWords = self::checkText($text);
        
        if (empty($foundWords)) {
            return [
                'has_violation' => false,
                'message' => null,
                'is_banned' => false
            ];
        }
        
        $user = User::findOne($userId);
        if (!$user) {
            return [
                'has_violation' => true,
                'message' => 'Пользователь не найден',
                'is_banned' => false
            ];
        }
        
        $user->addViolation($commentId, 'insult', 'Запрещенные слова: ' . implode(', ', $foundWords));
        
        return [
            'has_violation' => true,
            'message' => $user->ban_reason ?? 'Ваше сообщение содержит запрещенные выражения. Нарушение зафиксировано.',
            'is_banned' => $user->isBanned()
        ];
    }
}
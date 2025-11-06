## 05 - LLM Closed

- AI ajanlarına kapalı politika:
  ```php
  $aiHeaderRules = [
    ['pattern'=>'/*','xrobots'=>'noindex, noai']
  ];
  $aiAgents = [
    '*' => ['allow'=>[], 'disallow'=>['/*']]
  ];
  ```
- Böylece `X-Robots-Tag: noindex, noai` header basılır, robots.txt içinde tüm AI ajanlarına Disallow çıkılır.

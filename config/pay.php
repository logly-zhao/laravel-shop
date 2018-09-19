<?php

return [
    'alipay' => [
        'app_id'         => '2016092100561268',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzW1S/92Gg/PJ9MO/zO/Kjgd78379ejXUoKgmaj2mTS9R9ntcblz1VKifMUd3ftVkjoPz+iddQNDiTXr35+TbnYqr5L5YN86SJoa/WdJz0lN96KVwkClt0gQ65fAqFrtjuGF20nTkq1CYNN4ykUBiRFYZ50tIGfHmmjQq4mLRap9A3hSlqwYe3eCULKwa/CLWljwfBvyp+CzLK29oas88SJUf5GFDIC17Fui95MTGM7fKoA9K4XRalVvzEB7Oj3KcT+i/yzV+xj5oin42kMilz2YfCR12xeEO7yKBYSwJf79uJs9hdN49osYZtERnH3DFGFm3bnyPzl/55J9VpNxdSQIDAQAB',
        'private_key'    => 'MIIEogIBAAKCAQEAwVSjHuX2J3LmpZXKB8lfdenXH4nW1pbiJAzdjyYtFPie04/aDxjVMU+IohfRJTBCJ3HyMmh+87XdAuuHGFU/iBBLlEi3++zZmZvjyBzqi5uX1H/CkpZsg8/scADAk3gJRFXIxpndqUn8K3gHSmSTxAhwTn25uKVs5KTq9iYiHWLLS6kzxmdsWzVHJSyF5JLbM3SDIUnqV4Ea8DwMfnPi/+kZjaoT5k+ZRzcECORYt0Vl0karWje+GY5Ot0wAi227qDMIspNADeoAEDpxLwhv4Aj/o7/Ien673J9kFQQRzKC/UdLvGM/BJEfBY2RZMYT3ZTWyNHuxQfzYxEOSb6IgiQIDAQABAoIBAHjOYn/UQtSzv9yIlQ34j2YnEP3sLAs7jWPphikFOfZ4xQZ+RUxZwB5oDID9UPtkBHKRiT5mfa3cXpaMhoZ1/pUPWar3JcCOaArMlrFdIFLMjzuB+huPU4+qRZxXW/7Ka0AAOs2fKBu415JyFlwuLQe6HjNt7Les5zaYtxnJClhBZRClFtahd786giUMiUUvSY/QK02KvgY0JlAOwUA0/McOhcywgx6RAJ2P1Pud6fZXcoL8lfCDB0kKAV29qJW4M89JwVAyTX9AYHSP/vwgzJoGhzPHvHvjl7ppERTRryUVdHxSDvf1clu66AMBy6cb50Lo/gWPMrpdxj3m5NMYMAECgYEA7dMHcEdf+LZ9JCVNjwJJdOF5DGsN0AMQ3qlKoQAohnW6PhRUzg97jNR0NbXvRELhA7n2DlC/vo4kw20rmOGLPPYfsyJDr+qHw24U+XlXSHdQwdrmvahacteL5W3hy0eU8vLG1KEMC+K9bgp06X8RrAVdD3jrVvnku80vbEflyUECgYEA0BsZo+qyvs3DYV1MJW6pIE/ryorjdpeJtlMdHsjrgvjVgm+95PncAVrnTBO6FWDUWYcxM5Wj26mClX4+K9SwLO7Nd4IV3b5glp6+LN6NJHDaCk9hd56UyNt2itGGXsg6/2uPDvP2Dap6WVyTIG0KIlLkYdQ8bznWmLdiWOcXfUkCgYAcZy6lPzD8EDdj4LyhjsFdHyyIyiRNKoCSRx+tmMo+PMMwdNcoAPtdd2YloAPiYCZlfokfbo9n36wOVvR0/sLVVgO1LittUBJEydnhbZd6BR30+QLDtd6PhqSjkvr7fyIP3eTXMiCxLrQ29zK9TDq9E6rolSVuLswdjcTTJscPwQKBgCqasbgeL3PimjJPsGwlaJiF5Cb2IjVANuBNybNovNZ2bPekOsMq6ACbBKlNjJYd2btucwM1inNJE8PZMY81ns/EH+pshl98Pzf2zcZP1aN1Ox5LPBAKNjmcvHsVBCZZZcKu2X3dMi2ADkZHLpO/3whB4iGpGqpGtAlV2duLjAKJAoGAU/C+M1f5EZsMo7OTIdEXQvd4qXEI+6dCqyg+agjsrIdwpVeQC8ZHGhi4ibrLWsWqlTY/9tLLjLNZzbpByO7rwREVBrtiUb4mBSsuqYs5ISQvKUOLU9JNzgpHP2I4TiVW55TlKYHbycud5mKE1JbgrqYy3XZj4OC9seEQYD2IzYk=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],
    'alipay_user' => [
        'app_id'         => '',
        'ali_public_key' => '',
        'private_key'    => '',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],
    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];

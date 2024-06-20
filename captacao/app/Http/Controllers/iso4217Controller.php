<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Factory\iso4217Factory;
use App\Repository\iso4217Repository;
use Symfony\Component\DomCrawler\Crawler;

class Iso4217Controller extends Controller
{
    protected $iso4217Repository;

    public function __construct(iso4217Repository $iso4217Repository)
    {
        $this->iso4217Repository = $iso4217Repository;
    }

    public function store(Request $request)
    {
        $client = iso4217Factory::createClient();
        $website;
 
        try {
            $website = $client->request('GET', 'https://pt.wikipedia.org/wiki/ISO_4217');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao acessar o site: ' . $e->getMessage()
            ], 500);
        }

        $codes = array_merge(
            $request->input('code_list', []),
            (array)$request->input('code'),
            $request->input('number_lists', []),
            (array)$request->input('number', [])
        );

        if (empty($codes)) {
            return response()->json([
                'error' => 'Nenhum código ou número fornecido.'
            ], 400);
        }

        $table;
        try {
            $table = $website->filter('table.wikitable')->first();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao processar a tabela: ' . $e->getMessage()
            ], 500);
        }

        $result = [];
        $foundCodes = [];

        $table->filter('tr')->each(function (Crawler $row, $i) use (&$codes, &$result, &$foundCodes) {
            if ($i == 0) {
                return; // Ignora o cabeçalho
            }

            $columns = $row->filter('td');
            if ($columns->count() > 0) {
                $code = $columns->eq(0)->text();
                $number = $columns->eq(1)->text();
                if (in_array($code, $codes) || in_array($number, $codes)) {
                    $currencyData = [
                        'code' => $code,
                        'number' => $number,
                        'decimal' => $columns->eq(2)->text(),
                        'currency' => $columns->eq(3)->text(),
                        'currency_locations' => []
                    ];

                    $locations = $columns->eq(4)->filter('a');
                    $locations->each(function (Crawler $location) use (&$currencyData) {
                        $currencyData['currency_locations'][] = [
                            'location' => $location->text(),
                            'icon' => $location->filter('img')->count() > 0 ? $location->filter('img')->attr('src') : ''
                        ];
                    });

                    $result[] = $currencyData;
                    $foundCodes[] = $code; // Adiciona o código à lista de encontrados
                    $foundCodes[] = $number; // Adiciona o número à lista de encontrados

                    $this->iso4217Repository->updateOrCreate(['code' => $code], ['code' => $code]);
                }
            }
        });

        $invalidCodes = array_diff($codes, $foundCodes);
        if (!empty($invalidCodes)) {
            return response()->json([
                'error' => 'Código ou número fornecido inválido ou inexistente',
                'invalid_codes' => $invalidCodes
            ], 400);
        }

        return response()->json($result, 200);
    }
}

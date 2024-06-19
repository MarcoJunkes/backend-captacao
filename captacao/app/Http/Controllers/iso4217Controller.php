<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\iso4217;

class Iso4217Controller extends Controller
{
    public function store(Request $request)
    {
        $client = new Client();
        $website = $client->request('GET', 'https://pt.wikipedia.org/wiki/ISO_4217');
        
        $codes = $request->input('codes', []);
        if (!is_array($codes)) {
            $codes = [$codes];
        }

        // Inicializa arrays para armazenar resultados e códigos encontrados
        $table = $website->filter('table.wikitable')->first();
        $result = [];
        $foundCodes = [];

        // Processa a tabela para encontrar e armazenar dados relevantes
        $table->filter('tr')->each(function (Crawler $row, $i) use (&$codes, &$result, &$foundCodes) {
            if ($i == 0) {
                return; // Ignora o cabeçalho
            }
            
            $columns = $row->filter('td');
            if ($columns->count() > 0) {
                $code = $columns->eq(0)->text();
                if (in_array($code, $codes)) {
                    $currencyData = [
                        'code' => $code,
                        'number' => $columns->eq(1)->text(),
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
                    
                    iso4217::updateOrCreate(['code' => $code], ['code' => $code]);
                }
            }
        });

        // Verifica se todos os códigos fornecidos foram encontrados
        $invalidCodes = array_diff($codes, $foundCodes);
        if (!empty($invalidCodes)) {
            return response()->json([
                'error' => 'Código fornecido inválido ou inexistente.',
                'invalid_codes' => $invalidCodes
            ], 400);
        }

        return response()->json($result);
    }
}

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
        
        // Inicializa os arrays para armazenar os códigos e números recebidos
        $codes = [];
        
        // Obtém os parâmetros da requisição
        $codeList = $request->input('code_list', []);
        $code = $request->input('code');
        $numberList = $request->input('number_lists', []);
        $number = $request->input('number', []);
        
        // Adiciona os códigos e números aos arrays de códigos
        if (!empty($codeList)) {
            $codes = array_merge($codes, $codeList);
        }
        
        if ($code) {
            $codes[] = $code;
        }
        
        if (!empty($numberList)) {
            $codes = array_merge($codes, $numberList);
        }
        
        if (!empty($number)) {
            $codes = array_merge($codes, $number);
        }

        if (empty($codes)) {
            return response()->json([
                'error' => 'Nenhum código ou número fornecido.'
            ], 400);
        }

        // Inicializa arrays para armazenar resultados e códigos/números encontrados
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
                    
                    iso4217::updateOrCreate(['code' => $code], ['code' => $code]);
                }
            }
        });

        // Verifica se todos os códigos ou números fornecidos foram encontrados
        $invalidCodes = array_diff($codes, $foundCodes);
        if (!empty($invalidCodes)) {
            return response()->json([
                'error' => 'Código ou número fornecido inválido ou inexistente',
                'invalid_codes' => $invalidCodes
            ], 400);
        }

        return response()->json($result);
    }
}

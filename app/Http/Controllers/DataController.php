<?php
/**
 * Created by PhpStorm.
 * User: larjo
 * Date: 14/5/2016
 * Time: 7:24 μμ
 */

namespace App\Http\Controllers;


use App\Filter;
use App\Triple;
use App\TriplePattern;
use Asparagus\QueryBuilder;
use Cache;
use EasyRdf_Http;
use EasyRdf_Http_Client;
use EasyRdf_Literal;
use EasyRdf_Literal_Decimal;
use EasyRdf_Literal_Integer;
use EasyRdf_Namespace;
use EasyRdf_Sparql_Client;
use EasyRdf_Sparql_Result;
use GuzzleHttp\Client;


class DataController extends Controller
{

    private $sparql;

    function get()
    {
        EasyRdf_Http::setDefaultHttpClient(new EasyRdf_Http_Client(null, ['maxredirects' => 5,
            'useragent' => 'EasyRdf_Http_Client',
            'timeout' => 100]));
        $this->sparql = new EasyRdf_Sparql_Client(config("sparql.endpoint"));
        foreach (config("sparql.prefixes") as $prefix => $uri) {
            //dd($prefix);
            EasyRdf_Namespace::set($prefix, $uri);
        }

        $queryBuilder = new QueryBuilder(config("sparql.prefixes"));
        $categories = config("drugs.categories");
        $categoryUris = array_map(function ($category) {
            return "<http://bio2rdf.org/drugbank_vocabulary:$category>";
        }, $categories);
        $categoryUrisImploded = implode(", ", $categoryUris);

        $drugsExcluded = config("drugs.excluded");
        $drugsExcludedEscaped = array_map(function ($drug) {
            return "'$drug'";
        }, $drugsExcluded);
        $drugsExcludedImploded = implode(', ', $drugsExcludedEscaped);


//dd($drugsExcludedImploded);

        $allDrugsQueryBuilder = new QueryBuilder(config("sparql.prefixes"));
        $allDrugsQueryBuilder->selectDistinct(["?id", "?title"]);
        $allDrugsQueryBuilder->filter("?category IN ($categoryUrisImploded)");
        $allDrugsQueryBuilder->where("?id", "dcterms:title", "?title");
        $allDrugsQueryBuilder->filter("STR(?title) NOT IN ($drugsExcludedImploded)");
        $allDrugsQueryBuilder->where("?id", "a", "<http://bio2rdf.org/drugbank_vocabulary:Drug>");
        $allDrugsQueryBuilder->where("?id", "<http://bio2rdf.org/drugbank_vocabulary:category>", "?category");
        $allDrugsQueryBuilder->orderBy("?title");

        $drugsPlayed = request("playedDrugs");
        if (count($drugsPlayed) > 0) {
            $drugsPlayedUris = array_map(function ($drug) {
                return "<$drug>";
            }, $drugsPlayed);
            $drugsPlayedUrisImploded = implode(', ', $drugsPlayedUris);
            $allDrugsQueryBuilder->filter("?id NOT IN ($drugsPlayedUrisImploded)");

        }
        // echo $allDrugsQueryBuilder->format();die;


        $drugsResult = $this->sparql->query(
            $allDrugsQueryBuilder->getSPARQL()
        );
        $drugs = $this->rdfResultsToArray($drugsResult);

        $index = rand(0, count($drugs));

        $drugId = $drugs[$index]["id"];

        if (Cache::has("drug/{$drugId}"))
            $drug = Cache::get("drug/{$drugId}");
        else {

            $queryBuilder->selectDistinct(["?value", "?description"]);

            $queryBuilder->where("<$drugId>", "dcterms:title", "?value");
            $queryBuilder->where("<$drugId>", "dcterms:description", "?description");


//echo $queryBuilder->format();die;

            /** @var EasyRdf_Sparql_Result $drugsResult */
            $drugResult = $this->sparql->query(
                $queryBuilder->getSPARQL()
            );

            $drug = $this->rdfResultsToArray($drugResult)[0];
            /** @var TriplePattern[] $patterns */


            $drugName = $drug["value"];
            $re = "/$drugName/i";
            $subst = "Lost Drug";

            $drug["description"] = preg_replace($re, $subst, $drug["description"]);
            $drug["description"] = preg_replace("/\\[(drugbank|Wikipedia|PubChem).*\\]/i", "", $drug["description"]);


            $patterns = [
                new TriplePattern("indication", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:indication>", "?indic_res"),
                    new Triple("?indic_res", "dcterms:description", "?indication")
                ]),

                new TriplePattern("mechanism", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:mechanism-of-action>", "?mechanism_res"),
                    new Triple("?mechanism_res", "dcterms:description", "?mechanism"),
                ]),


                new TriplePattern("absorption", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:absorption>", "?absorption_res"),
                    new Triple("?absorption_res", "dcterms:description", "?absorption"),
                ]),


                new TriplePattern("biotransformation", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:biotransformation>", "?biotransformation_res"),
                    new Triple("?biotransformation_res", "dcterms:description", "?biotransformation"),
                ]),


                new TriplePattern("elimination", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:route-of-elimination>", "?elimination_res"),
                    new Triple("?elimination_res", "dcterms:description", "?elimination"),
                ]),


                new TriplePattern("halflife", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:half-life>", "?halflife_res"),
                    new Triple("?halflife_res", "dcterms:description", "?halflife"),
                ]),


                new TriplePattern("target", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:target>", "?target_res"),
                    new Triple("?target_res", "rdfs:label", "?target"),
                ]),

                new TriplePattern("taxonomy", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:drug-classification-category>", "?taxonomy_res"),
                    new Triple("?taxonomy_res", "rdfs:label", "?taxonomy"),
                ]),


                new TriplePattern("pharmacodynamics", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:pharmacology>", "?dynamics_res"),
                    new Triple("?dynamics_res", "dcterms:description", "?pharmacodynamics"),
                ]),

                new TriplePattern("effect", [
                    new Triple("?s_drug", "a" ,"<http://bio2rdf.org/sider_vocabulary:Drug>"),
                    new Triple("?s_drug", "<http://purl.org/dc/terms/title>" ,"?s_title"),
                    new Triple("?s_drug", "<http://bio2rdf.org/sider_vocabulary:side-effect>", "?effect_res"),
                    new Filter("STR(?s_title)='".strtolower($drug["value"])."'"),
                    new Triple("?effect_res", "<http://purl.org/dc/terms/title>", "?effect"),
                ]),

                /*   new TriplePattern("structure", [
                       new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:calculated-properties>",  "?structure_res"),
                       new Triple("?structure_res", "<http://bio2rdf.org/drugbank_vocabulary:value>",  "?structure"),
                       new Triple("?structure_res", "a",  "<http://bio2rdf.org/drugbank_vocabulary:SMILES>"),
                   ]),*/

                new TriplePattern("category", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:category>", "?category_res"),
                    new Triple("?category_res", "rdfs:label", "?category"),
                ]),
                new TriplePattern("structure", [
                    new Triple("<$drugId>", "<http://bio2rdf.org/drugbank_vocabulary:x-pubchemcompound>", "?compound_res"),
                    new Triple("?compound_res", "<http://bio2rdf.org/bio2rdf_vocabulary:identifier>", "?structure"),
                ]),


            ];



            foreach ($patterns as $pattern) {
                $queryBuilder = new QueryBuilder(config("sparql.prefixes"));
                $queryBuilder->selectDistinct("?" . $pattern->name);
                foreach ($pattern->triples as $triple) {
                    if($triple instanceof Triple)
                        $queryBuilder->where($triple->subject, $triple->predicate, $triple->object);
                    elseif ($triple instanceof Filter)
                        $queryBuilder->filter($triple->filter);

                }

                echo $queryBuilder->format();
                $drugResult = $this->sparql->query(
                    $queryBuilder->getSPARQL()
                );

                $valuesArray = $this->rdfResultsToArray($drugResult);
                
                if($pattern->name=="structure" && count($valuesArray)>0){
                    $drug["molecule"] = $this->getMolecule($valuesArray[0]["structure"]);
                }
                
                
                if (count($valuesArray) > 1) {

                    $hub = ["name"=>$pattern->name, "value"=>"", "locked"=>false, "hub"=>true];
                    $i = 1;
                    $width = ceil(log10(count($valuesArray)+1));
                    foreach ($valuesArray as $drugElement) {

                        $value = $drugElement[$pattern->name];

                        $value = $this->getValue($value, [
                                "/\\[(drugbank|Wikipedia|PubChem).*\\]/i" => "",
                                "/$drugName/i" => "Lost Drug"
                            ]
                        );


                        $hub["children"][] = ["name" => $pattern->name."#".str_pad((string)$i, $width, "0", STR_PAD_LEFT), "value" => $value, "locked" => true];
                        $i++;
                    }

                    $drug["children"][] = $hub;

                }
                elseif (count($valuesArray) > 0) {

                    $value = $valuesArray[0][$pattern->name];

                    $value = $this->getValue($value, [
                            "/\\[(drugbank|wikipedia|PubChem).*\\]/i" => "",
                            "/$drugName/i" => "Lost Drug"
                        ]
                    );


                    $drug["children"][] = ["name" => $pattern->name, "value" => $value, "locked" => true];

                }

            }

            die;


            $costWeights = [
                "indication" => 5,
                "mechanism" => 5,
                "absorption" => 2,
                "biotransformation" => 2,
                "elimination" => 2,
                "halflife" => 1,
                "target" => 5,
                "taxonomy" => 10,
                "pharmacodynamics" => 10,
                "structure" => 2,
                "category" => 5,
                "effect" => 2,
            ];
            $drug["cost"] = 50;

            $sum = array_sum($costWeights);
            foreach ($drug["children"] as &$child) {
                $factor =  $drug["cost"]/ $sum;
                $child["cost"] = round(intval($factor * $costWeights[$child["name"]]));
                if(isset($child["children"]))
                    foreach ($child["children"] as &$sub_child) {
                        $sub_child["cost"] = round($child["cost"]/count($child["children"]));
                    }
            }


            $drug["name"] = "Lost drug";
            $drug["locked"] = true;
            $drug["root"] = true;
            $drug["id"] = $drugId;
            Cache::forever("drug/{$drugId}", $drug);

        }


        $data = ["drugs" => $drugs, "drug" => $drug];

        return $data;

    }

    protected function getValue(string $value, array $exclusions)
    {



        foreach ($exclusions as $re => $subst) {
            $value = preg_replace($re, $subst, $value);
        }
        return $value;


    }


    protected function rdfResultsToArray(EasyRdf_Sparql_Result $result)
    {
        $results = [];
        foreach ($result as $row) {
            $added = [];
            $fields = $result->getFields();
            foreach ($fields as $field) {
                if (!isset($row->$field)) continue;
                $value = $row->$field;
                if ($value instanceof EasyRdf_Literal) {
                    /** @var EasyRdf_Literal $value */
                    $val = $value->getValue();
                    if ($value instanceof EasyRdf_Literal_Decimal)
                        $val = floatval($val);
                    elseif ($value instanceof EasyRdf_Literal_Integer)
                        $val = intval($val);
                    $added[$field] = $val;
                } else {
                    /** @var \EasyRdf_Resource $value */
                    $added[$field] = $value->dumpValue('text');
                }
            }
            $results[] = $added;
        }
        return $results;
    }



    public function getMolecule($id){
        $client = new Client();
        $res = $client->request('GET', "http://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/$id/SDF", [

        ]);
   /*     echo $res->getStatusCode();
// 200
        echo $res->getHeaderLine('content-type');*/
// 'application/json; charset=utf8'
        return  $res->getBody();

    }

}
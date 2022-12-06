<?php

namespace App\Repositories;

use App\Empresa;
use App\Estoque;
use App\Events\MakeLog;
use App\Http\Controllers\Help;
use App\Http\interfaces\ProductInterface as InterfacesProductInterface;
use App\Http\Resources\ProductResource;
use App\Materiais;
use App\Product;
use App\Produtos_Materias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository implements InterfacesProductInterface
{

    public function __construct()
    {
    }
    public function listAll(Request $request){
        try{
            if($request->filled('opcao')){
                $op = $request->opcao;
                switch ($op){
                    case "Produtos mais baratos":
                        return $this->filterByLowestValue($request);
                        break;
                    case "Produtos mais caros":
                        return $this->filterByExpansiveValue($request);
                        break;
                }
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $PRODUCTS = DB::table('EMPRESAS')->where('EMPRESAS.ID', $empresa->ID)->join('ESTOQUES', 'ESTOQUES.EMPRESA_ID', '=', 'EMPRESAS.ID')->
                join('PRODUCTS', 'PRODUCTS.ID', '=', 'ESTOQUES.PRODUCT_ID')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')->select(
                    'CATEGORIAS.ID_CATEGORIA', 'CATEGORIAS.NOME_C',  'PRODUCTS.ID', 'PRODUCTS.NOME', 'PRODUCTS.VALOR', 'ESTOQUES.QUANTIDADE'
                )->orderBy('ID', 'desc')->paginate(20);
                return $PRODUCTS;
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }

    public function filterByLowestValue(Request $request){
        try{
            if($request->filled('pdf')){
                $user = auth()->user();
                $empresa = $user->empresa;
                $PRODUCTS = DB::table('EMPRESAS')->where('EMPRESAS.ID', $empresa->ID)->join('ESTOQUES', 'ESTOQUES.EMPRESA_ID', '=', 'EMPRESAS.ID')->
                join('PRODUCTS', 'PRODUCTS.ID', '=', 'ESTOQUES.PRODUCT_ID')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')->
                join('MEDIDAS', 'MEDIDAS.ID', '=', 'PRODUCTS.ID_MEDIDA')->select(
                   'PRODUCTS.ID', 'PRODUCTS.NOME', 'CATEGORIAS.NOME_C', 'MEDIDAS.NOME_REAL',  'PRODUCTS.VALOR', 'ESTOQUES.QUANTIDADE'
                )->orderBy('PRODUCTS.VALOR', 'asc')->get();
                return $PRODUCTS;
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $PRODUCTS = Empresa::FindOrFail($empresa->ID)->product()->with([
                    'category' => function($query){
                        $query->select('ID_CATEGORIA', 'NOME_C');
                    }
                ])->orderBy('PRODUCTS.VALOR', 'asc')->paginate(20);
                return $PRODUCTS;
            }

        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }
    public function filterByExpansiveValue(Request $request){
        try{
            if($request->filled('pdf')){
                $user = auth()->user();
                $empresa = $user->empresa;
                $PRODUCTS = DB::table('EMPRESAS')->where('EMPRESAS.ID', $empresa->ID)->join('ESTOQUES', 'ESTOQUES.EMPRESA_ID', '=', 'EMPRESAS.ID')->
                join('PRODUCTS', 'PRODUCTS.ID', '=', 'ESTOQUES.PRODUCT_ID')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')->
                join('MEDIDAS', 'MEDIDAS.ID', '=', 'PRODUCTS.ID_MEDIDA')->select(
                   'PRODUCTS.ID', 'PRODUCTS.NOME', 'CATEGORIAS.NOME_C', 'MEDIDAS.NOME_REAL',  'PRODUCTS.VALOR', 'ESTOQUES.QUANTIDADE'
                )->orderBy('PRODUCTS.VALOR', 'desc')->get();
                return $PRODUCTS;
            }else{
                $user = auth()->user();
                $empresa = $user->empresa;
                $PRODUCTS = Empresa::FindOrFail($empresa->ID)->product()->with([
                    'category' => function($query){
                        $query->select('ID_CATEGORIA', 'NOME_C');
                    }
                ])->orderBy('PRODUCTS.VALOR', 'desc')->paginate(20);
                return $PRODUCTS;
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function findAllProductByCategory($id){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $PRODUCTS = DB::table('EMPRESAS')->where('EMPRESAS.ID', $empresa->ID)->join('ESTOQUES', 'ESTOQUES.EMPRESA_ID', '=', 'EMPRESAS.ID')->
            join('PRODUCTS', 'PRODUCTS.ID', '=', 'ESTOQUES.PRODUCT_ID')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')->select(
                'CATEGORIAS.ID_CATEGORIA', 'CATEGORIAS.NOME_C',  'PRODUCTS.ID', 'PRODUCTS.NOME', 'PRODUCTS.VALOR', 'ESTOQUES.QUANTIDADE'
            )->where('CATEGORIAS.ID_CATEGORIA', $id)->paginate(20);
            return $PRODUCTS;
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ]
            );
        }
    }
    public function show($id){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $PRODUCTS = Product::where('ID', $id)->with(['category' => function($query){
                $query->select('ID_CATEGORIA', 'NOME_C');
                }
            ])->with(['medida' => function($query){
                $query->select('ID', 'NOME');
            }])->firstOrFail();
            $materias = collect(new Materiais());
            foreach($PRODUCTS->materias as $matItem){
                $qntd = $matItem->QUANTIDADE;
                $valor = $matItem->VALOR;
                $matItem = Materiais::FindOrFail($matItem->ID_MATERIA);
                $matItem->QUANTIDADE = $qntd;
                $matItem->VALOR = $valor;
                $materias->push($matItem);
            }
            $PRODUCTS->MATERIAS = $materias;
            return response()->json($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                $e->getMessage(), 400
            );
        }
    }
    public function store(Request $request){
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            $helper = new Help();
            $helper->startTransaction();
            $validatedData = $request->validate([
                'NOME' => ['required', 'unique:PRODUCTS', 'max:60', 'min:2'],
                'DESC' => ['required', 'max:120', 'min:4'],
                'VALOR'=> ['required', 'min:0'],
                'ID_CATEGORIA' => ['required'],
                'ID_MEDIDA' => ['required'],
                'MATERIAIS' => ['required'],
                'quantidade_inicial' => ['required', 'min:0']
            ]);
            if($validatedData){
                $produto = new Product();
                $produto->NOME = $request->NOME;
                $produto->DESC = $request->DESC;
                $produto->VALOR = $request->VALOR;
                $produto->ID_CATEGORIA = $request->ID_CATEGORIA;
                $produto->ID_MEDIDA = $request->ID_MEDIDA;
                $produto->save();
                $materias = collect(new Materiais());
                foreach($request->MATERIAIS as $material){
                    $produto_material = new Produtos_Materias();
                    $materia = (object) $material;
                    $produto_material->ID_PRODUTO = $produto->ID;
                    $produto_material->ID_MATERIA = $materia->ID;
                    $produto_material->QUANTIDADE = $materia->QUANTIDADE;
                    $produto_material->CUSTO = $materia->CUSTO;
                    $produto_material->save();
                    $materias->push($materia);
                }
                $mtController = new MateriaisRepository();
                if($produto){
                    $quantidade = $request->quantidade_inicial;
                    $mtController->removeQuantidadeMaterial($materias, $quantidade);
                    $estoque = new EstoqueRepository();
                    $estoque->storeProdutoInEstoque($produto->ID, $quantidade);
                    event(new MakeLog("Produtos", "", "insert", " $produto->NOME", "", $produto->ID, $empresa->ID, $user->ID));
                    $helper->commit();
                    return response()->json(
                        $produto
                    );
                }
            }
        }catch(\Exception $e){
            $helper->rollbackTransaction();
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function update(Request $request, $id){
        $helper = new Help();
        $user = auth()->user();
        $empresa = $user->empresa;
        try{
            $PRODUCT = Product::FindOrFail($id);
            $tmp = $PRODUCT;
            $PRODUCT->update($request->all());
            return response()->json(
                [
                    "message" => "Produto editado com sucesso"
                ],200
            );
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }
    public function destroy($id){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $estoque = Estoque::where('EMPRESA_ID', $user->empresa->ID)->where('PRODUCT_ID', $id);
            $estoque->delete();
            $PRODUCT = Product::where('ID',$id)->first();
            event(new MakeLog("Produtos", "", "delete", "", json_encode($PRODUCT), $PRODUCT->ID, $empresa->ID, $user->ID));
            $PRODUCT->delete();
            return response()->json(
                [
                    "message" => "Produto excluido com sucesso !"
                ],200
                );
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }
    public function findLucroByProduto($id){
        try{
            $user = auth()->user();
            $produto = Product::FindOrFail($id);
            $lucro = $produto->VALOR;

            $materias = collect(new Materiais());
            foreach($produto->materias as $matItem){
                $qntd = $matItem->QUANTIDADE;
                $matItem = Materiais::FindOrFail($matItem->ID_MATERIA);
                $matItem->QUANTIDADE = $qntd;
                $materias->push($matItem);
            }
            foreach($materias as $material){
                $lucro -= ($material->CUSTO * $material->QUANTIDADE);
            }
            return response()->json($lucro);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
    public function search(Request $request){
        try{
            $search = $request->search;
            $search = ucwords($search);
            $PRODUCTS = Product::query()->where('NOME', 'LIKE', '%'. $search.'%')
                                ->orWhere('DESC', 'LIKE', '%'.$search.'%')->paginate(20);
            return ProductResource::collection($PRODUCTS);
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
                );
        }
    }
    public function descontaQuantidadeMaterial($id, $quantidade){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            $product = Product::FindOrFail($id);
            $materias = collect(new Materiais());
            foreach($product->materias as $matItem){
                $qntd = $matItem->QUANTIDADE;
                $matItem = Materiais::FindOrFail($matItem->ID_MATERIA);
                $matItem->QUANTIDADE = $qntd;
                $materias->push($matItem);
            }
            foreach($materias as $mat){
                $tmp = Materiais::FindOrFail($mat->ID);
                $antigoVl = $tmp;
                $tmp->QUANTIDADE -= ($mat->QUANTIDADE * $quantidade);
                if($tmp->QUANTIDADE < 0){
                    return false;
                }else{
                    $tmp->save();
                }
            }
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
    public function countProducts(){
        try{
            $user = auth()->user();
            $empresa = $user->empresa;
            return DB::table('PRODUCTS')->join('CATEGORIAS', 'CATEGORIAS.ID_CATEGORIA', '=', 'PRODUCTS.ID_CATEGORIA')
            ->where('CATEGORIAS.ID_EMPRESA', '=', $empresa->ID)->where('PRODUCTS.DELETED_AT', '=', null)->count();
        }catch(\Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ],400
            );
        }
    }
}

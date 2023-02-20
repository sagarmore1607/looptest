<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Orders;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Orders::with('customers','products')->paginate(10);
        return [
            'status'=> 200,
            'data'=> $orders
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer'=> 'required'
        ]);

        if($validator->fails())
        {
            return [
                'status'=> 400,
                'message'=> $validator->errors()
            ];
        }

        $addOrder = Orders::insert([
            'customer'=> $request->customer
        ]);
        if($addOrder)
        {
            return [
                'status'=> 201,
                'message'=> 'Order added' 
            ];
        }
        else
        {
            return [
                'status'=> 400,
                'message'=> 'Something went wrong' 
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Orders::with('customers','products')->where('id',$id)->first();
        if($order)
        {
            return [
                'status'=> 200,
                'data'=> $order
            ];
        }
        else
        {
            return [
                'status'=> 400,
                'message'=> 'Order not found' 
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer'=> 'required'
        ]);

        if($validator->fails())
        {
            return [
                'status'=> 400,
                'message'=> $validator->errors()
            ];
        }

        $updateOrder = Orders::where('id',$id)->update([
            'customer'=> $request->customer
        ]);
        if($updateOrder)
        {
            return [
                'status'=> 200,
                'message'=> 'Order updated' 
            ];
        }
        else
        {
            return [
                'status'=> 400,
                'message'=> 'Something went wrong' 
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteOrder = Order::where('id',$id)->delete();
        if($deleteOrder)
        {
            return [
                'status'=> 200,
                'message'=> 'Order deleted' 
            ];
        }
        else
        {
            return [
                'status'=> 400,
                'message'=> 'Something went wrong' 
            ];
        }
    }

    public function addProductToOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id'=> 'required'
        ]);

        if($validator->fails())
        {
            return [
                'status'=> 400,
                'message'=> $validator->errors()
            ];
        }

        $getOrder = Orders::where(['id'=> $id, 'payed'=> 0])->first();
        if(!$getOrder)
        {
            return [
                'status'=> 400,
                'message'=> "Order can't be updated"
            ];
        }

        $updateOrder = Orders::where('id',$id)->update([
            'product_id'=> $request->product_id
        ]);
        if($updateOrder)
        {
            return [
                'status'=> 200,
                'message'=> 'Product attached' 
            ];
        }
        else
        {
            return [
                'status'=> 400,
                'message'=> 'Something went wrong' 
            ];
        }
    }

    public function orderPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'order_id'=> 'required',
            'customer_email'=> 'required',
            'value'=> 'required'
        ]);

        if($validator->fails())
        {
            return [
                'status'=> 400,
                'message'=> $validator->errors()
            ];
        }

        if($id != $request->order_id)
        {
            return [
                'status'=> 400,
                'message'=> "Order id is different"
            ];
        }

        $getOrder = Orders::where(['id'=> $id,'payed'=> 0])->first();
        if(!$getOrder)
        {
            return [
                'status'=> 400,
                'message'=> "Order can't be updated"
            ];
        }

        $customer_email = trim(strtolower($request->email));
        $customer_id = Customer::where('email',$customer_email)->value('id');
        if(!$customer_id)
        {
            return [
                'status'=> 400,
                'message'=> "Customer not found with given customer_email"
            ];
        }

        // API call for payment
        $paymentRequest = Http::post('https://superpay.view.agentur-loop.com/pay',
        [
            'order_id'=> $id,
            'customer_email'=> $customer_email,
            'value'=> $request->value
        ]);

        if($paymentRequest->failed())
        {
            return [
                'status'=> $paymentRequest->status(),
                'message'=> $paymentRequest->json()['message']
            ];
        }

        $message = $paymentRequest->json()['message'];
        $updateOrder = Orders::where(['id'=> $id,'payed'=> 0])->update([
            'customer'=> $customer_id,
            'payed'=> 1
        ]);
        if($updateOrder)
        {
            return [
                'status'=> 200,
                'message'=> $message 
            ];
        }
        else
        {
            return [
                'status'=> 400,
                'message'=> 'Something went wrong'
            ];
        }
    }
}

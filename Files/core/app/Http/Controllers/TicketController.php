<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\SupportCredit;
use App\Models\AdPromote;
use Auth;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Image;
use Session;
use Validator;

class TicketController extends Controller
{

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }


    // Support Ticket
    public function supportTicket()
    {
        if (Auth::id() == null) {
            abort(404);
        }
        $page_title = "Support Tickets";
        $supports = SupportTicket::where('user_id', Auth::id())->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.support.index', compact('supports', 'page_title'));
    }

    public function requestBuyCredit($id)
    {
        if (Auth::id() == null) {
            abort(404);
        }
        $page_title = "Acquista Crediti";
        $supports = SupportCredit::where('user_id', Auth::id())->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'sections.requestBuyCredit.index', compact('supports', 'page_title'))->with(['product_id'=>$id]);
    }

    public function openSupportTicket()
    {
        if (!Auth::user()) {
            abort(404);
        }
        $page_title = "Support Tickets";
        $user = Auth::user();
        return view($this->activeTemplate . 'user.support.create', compact('page_title', 'user'));
    }


    public function buycredit($id)
    {
        if (!Auth::user()) {
            abort(404);
        }
        $page_title = "Buy new credit";
        $user = Auth::user();
        return view($this->activeTemplate . 'sections.requestBuyCredit.create', compact('page_title', 'user'))->with(['product_id' => $id]);
    }

    ///Buy new credit  ///////////////////////////////

    public function buynewcredit(Request $request)
    {
        $message = new SupportMessage();
        $adPromot = new AdPromote();

        $files = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf','doc','docx');

        $this->validate($request, [
            // 'attachments' => [
            //     'max:4096',
            //     function ($attribute, $value, $fail) use ($files, $allowedExts) {
            //         foreach ($files as $file) {
            //             $ext = strtolower($file->getClientOriginalExtension());
            //             if (($file->getSize() / 1000000) > 2) {
            //                 return $fail("Images MAX  2MB ALLOW!");
            //             }
            //             if (!in_array($ext, $allowedExts)) {
            //                 return $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed");
            //             }
            //         }
            //         if (count($files) > 5) {
            //             return $fail("Maximum 5 files can be uploaded");
            //         }
            //     },
            // ],
            // 'name' => 'required|max:191',
            // 'email' => 'required|email|max:191',
            // 'subject' => 'required|max:100',
            // 'message' => 'required',
        ]);

        $user = auth()->user();
        $adPromot->user_id = $user->id;
        $adPromot->ad_id = $request->product_id;
        // $random = rand(100000, 999999);
        $adPromot->package_id = 1;
        $adPromot->gateway_id = 1;
        $adPromot->status = 0;
        $adPromot->running = 1;
        $adPromot->contactName = $request->contactName;
        $adPromot->contactEmail = $request->contactEmail;
        $adPromot->subject = $request->subject;
        $adPromot->created_at = Carbon::now();
        $adPromot->save();

        $message->supportticket_id = $adPromot->id;
        // $message->message = $request->message;
        $message->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New acquista Crediti has requested';
        $adminNotification->click_url = route('admin.credit.view',$adPromot->id);
        $adminNotification->save();


        $path = imagePath()['ticket']['path'];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as  $file) {
                try {
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->attachment = uploadFile($file, $path);
                    $attachment->save();
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Non pu?? caricare il tuo ' . $file];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }
        $notify[] = ['success', 'Acquisto di crediti inviato con successo!'];
        return redirect()->route('user.ad.promotion.log', ['id'=>$request->product_id])->withNotify($notify);
    }



    public function storeSupportTicket(Request $request)
    {
        $ticket = new SupportTicket();
        $message = new SupportMessage();

        $files = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf','doc','docx');


        $this->validate($request, [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($files, $allowedExts) {
                    foreach ($files as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > 2) {
                            return $fail("Images MAX  2MB ALLOW!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf, doc, docx files are allowed");
                        }
                    }
                    if (count($files) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'name' => 'required|max:191',
            'email' => 'required|email|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);

        $user = auth()->user();
        $ticket->user_id = $user->id;
        $random = rand(100000, 999999);
        $ticket->ticket = $random;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New support ticket has opened';
        $adminNotification->click_url = route('admin.ticket.view',$ticket->id);
        $adminNotification->save();


        $path = imagePath()['ticket']['path'];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as  $file) {
                try {
                    $attachment = new SupportAttachment();
                    $attachment->support_message_id = $message->id;
                    $attachment->attachment = uploadFile($file, $path);
                    $attachment->save();
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Non pu?? caricare il tuo ' . $file];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }
        $notify[] = ['success', 'Un ticket ?? stato creato con successo'];
        return redirect()->route('ticket')->withNotify($notify);
    }

    public function viewTicket($ticket)
    {
        $page_title = "Support Tickets";
        $my_ticket = SupportTicket::where('ticket', $ticket)->latest()->first();
        $messages = SupportMessage::where('supportticket_id', $my_ticket->id)->latest()->get();
        $user = auth()->user();
        return view($this->activeTemplate. 'user.support.view', compact('my_ticket', 'messages', 'page_title', 'user'));

    }

    public function replyTicket(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $message = new SupportMessage();
        if ($request->replayTicket == 1) {
            $imgs = $request->file('attachments');
            $allowedExts = array('jpg', 'png', 'jpeg', 'pdf', 'doc','docx');

            $this->validate($request, [
                'attachments' => [
                    'max:4096',
                    function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                        foreach ($imgs as $img) {
                            $ext = strtolower($img->getClientOriginalExtension());
                            if (($img->getSize() / 1000000) > 2) {
                                return $fail("Images MAX  2MB ALLOW!");
                            }
                            if (!in_array($ext, $allowedExts)) {
                                return $fail("Only png, jpg, jpeg, pdf doc docx files are allowed");
                            }
                        }
                        if (count($imgs) > 5) {
                            return $fail("Maximum 5 files can be uploaded");
                        }
                    },
                ],
                'message' => 'required',
            ]);

            $ticket->status = 2;
            $ticket->last_reply = Carbon::now();
            $ticket->save();

            $message->supportticket_id = $ticket->id;
            $message->message = $request->message;
            $message->save();

            $path = imagePath()['ticket']['path'];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    try {
                        $attachment = new SupportAttachment();
                        $attachment->support_message_id = $message->id;
                        $attachment->attachment = uploadFile($file, $path);
                        $attachment->save();

                    } catch (\Exception $exp) {
                        $notify[] = ['error', 'Non pu?? caricare il tuo ' . $file];
                        return back()->withNotify($notify)->withInput();
                    }
                }
            }

            $notify[] = ['success', 'Il ticket ha avuto risposta!'];
        } elseif ($request->replayTicket == 2) {
            $ticket->status = 3;
            $ticket->last_reply = Carbon::now();
            $ticket->save();
            $notify[] = ['success', 'Il ticket di supporto ?? stato chiuso con successo!'];
        }
        return back()->withNotify($notify);
    }





    public function ticketDownload($ticket_id)
    {
        $attachment = SupportAttachment::findOrFail(decrypt($ticket_id));
        $file = $attachment->attachment;

        $path = imagePath()['ticket']['path'];
        $full_path = $path.'/'. $file;

        $title = str_slug($attachment->supportMessage->ticket->subject);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);


        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }

}

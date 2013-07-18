<?php

class TicketController extends AuthorizedController {

    public function getIndex()
    {
        $jobs = Job::where('member_id', '=', Auth::user()->id)->orderBy('id', 'desc')->paginate();
        // Show the page.
        //
        return View::make('ticket/index', compact('jobs'));
    }

    /**
     * getCreate
     *
     * @return mixed
     */
    public function getCreate()
    {
        $troubles = Trouble::all();

        // Show the page.
        //
        return View::make('ticket/create')->with('troubles', $troubles);
    }

    public function postCreate()
    {
        $rules = array(
            'title'      => 'required|min:4',
            'trouble_id' => 'required',
            'content'    => 'required|min:10',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails())
        {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $job   = new Job;
        $title = new Title;

        $job->member_id   = Auth::user()->id;
        $job->operator_id = Auth::user()->operator_id;
        $job->trouble_id  = e(Input::get('trouble_id'));
        $job->level       = e(Input::get('level'));
        $job->status      = '0';
        $job->assess      = '0';
        $job->repeat      = '0';

        if ($job->save())
        {
            $title->job_id     = $job->id;
            $title->title      = e(Input::get('title'));
            $title->content    = e(Input::get('content'));
            $title->member_id  = Auth::user()->id;
            $title->start_time = new DateTime;

            if ($title->save())
            {
                return Redirect::to("ticket")->with('success', '工单提交成功');
            }
        }

        return Redirect::to('ticket/create')->with('error', '工单提交失败');
    }

}
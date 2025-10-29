<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobDocument;
use App\Models\JobRequiredDocument;
use App\Models\JobQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JobUpdateController extends Controller
{

    /* =======================
       DETAILS / OVERVIEWS / LOGO
       ======================= */

    public function updateDetails(Request $request, Job $job)
    {
        $data = $request->validate([
            'employer_id' => ['nullable','integer','exists:employers,id'],
            'title' => ['required','string','max:255'],
            'location' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'country' => ['nullable','string','max:255'],
            'managed_by' => ['nullable','string','max:255'],
            'date_posted' => ['nullable','date'],
            'salary' => ['nullable','numeric'],
            'experience' => ['nullable', Rule::in(Job::$experience)],
            'category' => ['nullable', Rule::in(Job::$category)],
        ]);

        $job->update($data);

        return response()->json(['ok' => true]);
    }

    public function updateOverviews(Request $request, Job $job)
    {
        $data = $request->validate([
            'background' => ['nullable','string'],
            'assignment' => ['nullable','string'],
        ]);

        $job->description = $data['background'] ?? $job->description;
        $job->assignment_overview = $data['assignment'] ?? $job->assignment_overview;
        $job->save();

        return response()->json(['ok' => true]);
    }

    public function uploadLogo(Request $request, Job $job)
    {
        $request->validate([
            'company_logo' => ['required','image','max:4096'],
        ]);

        $path = $request->file('company_logo')->store('logos', 'public');
        $job->company_logo = $path;
        $job->save();

        return response()->json(['ok' => true, 'url' => asset("storage/{$path}")]);
    }

    /* =======================
       VIDEOS (simple file store for now)
       ======================= */

    public function uploadEmployerIntroVideo(Request $request, Job $job)
    {
        $request->validate([
            'video' => ['required','file','mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm,video/x-matroska,application/octet-stream','max:51200'], // 50MB
        ]);

        $path = $this->storeFile($request->file('video'), 'videos/employer');
        $job->employer_intro_video = $path;
        $job->save();

        return response()->json(['ok' => true, 'url' => asset("storage/{$path}")]);
    }

    public function deleteEmployerIntroVideo(Job $job)
    {
        if ($job->employer_intro_video) {
            Storage::disk('public')->delete($job->employer_intro_video);
            $job->employer_intro_video = null;
            $job->save();
        }
        return response()->json(['ok' => true]);
    }

    // public function uploadCandidateAssessmentVideo(Request $request, Job $job)
    // {
    //     $request->validate([
    //         'video' => ['required','file','mimetypes:video/mp4,video/quicktime,video/x-msvideo,application/octet-stream','max:51200'],
    //     ]);

    //     $path = $this->storeFile($request->file('video'), 'videos/candidate');
    //     $job->candidate_assessment_video = $path;
    //     $job->save();

    //     return response()->json(['ok' => true, 'url' => asset("storage/{$path}")]);
    // }

    // public function deleteCandidateAssessmentVideo(Job $job)
    // {
    //     if ($job->candidate_assessment_video) {
    //         Storage::disk('public')->delete($job->candidate_assessment_video);
    //         $job->candidate_assessment_video = null;
    //         $job->save();
    //     }
    //     return response()->json(['ok' => true]);
    // }

    /* =======================
       CAMPAIGN DOCUMENTS
       ======================= */

    public function documentsIndex(Job $job)
    {
        return response()->json([
            'ok' => true,
            'items' => $job->documents()->get()->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'url' => $d->url,
                'sort_order' => $d->sort_order,
            ]),
        ]);
    }

    public function documentsStore(Request $request, Job $job)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'file' => ['required','file','max:51200'], // 50MB
        ]);

        $path = $this->storeFile($request->file('file'), 'documents');

        $nextOrder = (int) ($job->documents()->max('sort_order') ?? 0) + 1;
        $doc = $job->documents()->create([
            'name' => $data['name'],
            'path' => $path,
            'sort_order' => $nextOrder,
        ]);

        return response()->json([
            'ok' => true,
            'item' => [
                'id' => $doc->id,
                'name' => $doc->name,
                'url' => $doc->url,
                'sort_order' => $doc->sort_order,
            ],
        ]);
    }

    public function documentsDestroy(Job $job, JobDocument $document)
    {
        abort_unless($document->job_id === $job->id, 404);

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return response()->json(['ok' => true]);
    }

    public function documentsReorder(Request $request, Job $job)
    {
        $data = $request->validate([
            'ids' => ['required','array','min:1'],
            'ids.*' => ['integer','exists:job_documents,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            JobDocument::where('job_id', $job->id)->where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    /* =======================
       REQUIRED CANDIDATE DOCUMENTS
       ======================= */

    public function reqDocsIndex(Job $job)
    {
        return response()->json([
            'ok' => true,
            'items' => $job->requiredDocuments()->get()->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'url' => $d->url,
                'sort_order' => $d->sort_order,
            ]),
        ]);
    }

    public function reqDocsStore(Request $request, Job $job)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'file' => ['nullable','file','max:51200'],
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $this->storeFile($request->file('file'), 'required_documents');
        }

        $nextOrder = (int) ($job->requiredDocuments()->max('sort_order') ?? 0) + 1;
        $doc = $job->requiredDocuments()->create([
            'name' => $data['name'],
            'path' => $path,
            'sort_order' => $nextOrder,
        ]);

        return response()->json([
            'ok' => true,
            'item' => [
                'id' => $doc->id,
                'name' => $doc->name,
                'url' => $doc->url,
                'sort_order' => $doc->sort_order,
            ],
        ]);
    }

    public function reqDocsDestroy(Job $job, JobRequiredDocument $document)
    {
        abort_unless($document->job_id === $job->id, 404);

        if ($document->path) Storage::disk('public')->delete($document->path);
        $document->delete();

        return response()->json(['ok' => true]);
    }

    public function reqDocsReorder(Request $request, Job $job)
    {
        $data = $request->validate([
            'ids' => ['required','array','min:1'],
            'ids.*' => ['integer','exists:job_required_documents,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            JobRequiredDocument::where('job_id', $job->id)->where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    /* =======================
       KEY COMPETENCY QUESTIONS
       ======================= */

    public function questionsIndex(Job $job)
    {
        return response()->json([
            'ok' => true,
            'items' => $job->questions()->get()->map(fn($q) => [
                'id' => $q->id,
                'question' => $q->question,
                'is_default' => $q->is_default,
                'is_enabled' => $q->is_enabled,
                'sort_order' => $q->sort_order,
            ]),
        ]);
    }

    public function questionsSeedDefaults(Job $job)
    {
        // Prevent reseeding if questions already exist
        if ($job->questions()->exists()) {
            return response()->json(['ok' => true, 'message' => 'Questions already exist.']);
        }

        // Official Default Key Competency Questions
        $defaults = [
            'Attention to Detail' => 'Describe at least one example when you failed in this area and explain what you learned from the experience?',
            'Customer Management' => 'Sometimes providing what a customer wants and providing what you know is in the best interests of the customer are not compatible. Please describe an occasion when you experienced this dilemma, explain how you resolved the issue and the eventual outcome.',
            'Market Understanding' => 'Technical background and commercial understanding: Would you describe your skill set as being more technically or commercially focused? Please give some narrative to support your answer.',
            'Sales and Business Development' => 'It is often said in business that "people buy people like them". Why do people buy from you and more importantly why do they continue to buy from you? What personality traits do you believe you have that make your selling style so effective? How do you think your customers would describe you professionally?',
            'Ambition' => 'Explain, in a few sentences, why you excel in this area?',
            'Leadership Skills' => 'How important do you believe this quality to be to the success of your work?',
            'Risk Assessment' => 'Describe at least one example when you failed in this area and explain what you learned from the experience?',
        ];

        $i = 1;
        foreach ($defaults as $title => $body) {
            $job->questions()->create([
                'question' => $body,
                'is_default' => true,
                'is_enabled' => true,
                'sort_order' => $i++,
            ]);
        }

        return response()->json(['ok' => true, 'message' => 'Default Key Competency Questions seeded successfully.']);
    }

    public function questionsCreate(Request $request, Job $job)
    {
        $data = $request->validate([
            'question' => ['required','string','max:1000'],
        ]);

        $nextOrder = (int) ($job->questions()->max('sort_order') ?? 0) + 1;
        $q = $job->questions()->create([
            'question' => $data['question'],
            'is_default' => false,
            'is_enabled' => true,
            'sort_order' => $nextOrder,
        ]);

        return response()->json([
            'ok' => true,
            'item' => [
                'id' => $q->id,
                'question' => $q->question,
                'is_default' => $q->is_default,
                'is_enabled' => $q->is_enabled,
                'sort_order' => $q->sort_order,
            ],
        ]);
    }

    public function questionsToggle(Job $job, JobQuestion $question)
    {
        abort_unless($question->job_id === $job->id, 404);

        $question->is_enabled = ! $question->is_enabled;
        $question->save();

        return response()->json(['ok' => true, 'is_enabled' => $question->is_enabled]);
    }

    public function questionsDestroy(Job $job, JobQuestion $question)
    {
        abort_unless($question->job_id === $job->id, 404);
        $question->delete();

        return response()->json(['ok' => true]);
    }

    public function questionsReorder(Request $request, Job $job)
    {
        $data = $request->validate([
            'ids' => ['required','array','min:1'],
            'ids.*' => ['integer','exists:job_questions,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            JobQuestion::where('job_id', $job->id)->where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    /* =======================
       TERMS & CONDITIONS
       ======================= */

    public function termsGet(Job $job)
    {
        return response()->json([
            'ok' => true,
            'terms_candidate' => $job->terms_candidate ?? '',
            'terms_employer' => $job->terms_employer ?? '',
        ]);
    }

    public function termsUpdate(Request $request, Job $job)
    {
        $data = $request->validate([
            'terms_candidate' => ['nullable','string'],
            'terms_employer' => ['nullable','string'],
        ]);

        $job->terms_candidate = $data['terms_candidate'] ?? null;
        $job->terms_employer = $data['terms_employer'] ?? null;
        $job->save();

        return response()->json(['ok' => true]);
    }

    /* ======================= */

    private function storeFile(UploadedFile $file, string $dir): string
    {
        return $file->store($dir, 'public');
    }
}

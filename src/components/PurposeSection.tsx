import { ImageWithFallback } from "./figma/ImageWithFallback";

export function PurposeSection() {
  return (
    <section className="py-16 px-4 sm:px-6 lg:px-8 bg-white/50">
      <div className="max-w-6xl mx-auto">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 className="mb-6 text-3xl sm:text-4xl">Why we started Therapair</h2>
            
            <blockquote className="text-xl sm:text-2xl italic mb-6 p-6 border-l-4 border-[var(--therapair-rosewood)] bg-[var(--therapair-calm-clay)]/10 rounded-r-xl">
              "I tried therapy, but it didn't feel right — the match just wasn't there."
            </blockquote>
            
            <div className="space-y-4 text-lg leading-relaxed opacity-90">
              <p>
                Too many people have this experience. Finding a therapist who truly understands your identity, 
                background, and therapeutic needs shouldn't be a game of chance.
              </p>
              <p>
                We believe that the right therapeutic relationship can be life-changing. That's why we're building 
                technology that considers not just availability and insurance, but cultural competence, therapeutic 
                approaches, and the unique aspects of who you are.
              </p>
              <p>
                Mental health care should be affirming, accessible, and tailored to you. That's the future we're creating.
              </p>
            </div>
            
            <div className="mt-8 p-4 bg-[var(--therapair-calm-clay)]/10 rounded-xl">
              <p className="text-sm opacity-80">
                <strong>About the founders:</strong> A team of mental health advocates, technologists, and lived-experience experts 
                committed to making therapy more inclusive and effective.
              </p>
            </div>
          </div>
          
          <div className="relative">
            <div className="rounded-2xl overflow-hidden shadow-lg">
              <ImageWithFallback
                src="https://images.unsplash.com/photo-1580133588950-de89bb18f35d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx3YXJtJTIwY2FyaW5nJTIwaGFuZHMlMjBzdXBwb3J0fGVufDF8fHx8MTc1OTkwMzcwNHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
                alt="Supportive hands representing care and connection"
                className="w-full h-96 object-cover"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}